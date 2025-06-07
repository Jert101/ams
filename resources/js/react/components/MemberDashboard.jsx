import React, { useState, useEffect } from 'react';

/**
 * Member Dashboard component
 * Displays attendance stats and upcoming events for members
 */
const MemberDashboard = ({ 
  presentCount = 0,
  absentCount = 0,
  excusedCount = 0,
  attendanceRate = 0,
  nextEvent = null,
  recentAttendances = []
}) => {
  // Calculate total events
  const totalEvents = presentCount + absentCount + excusedCount;
  
  // Progress tracking state
  const [progress, setProgress] = useState({
    lastLogin: null,
    loginStreak: 0,
    viewedEvents: [],
    attendedEvents: []
  });
  
  // Initialize progress from memory bank
  useEffect(() => {
    // Load from memory bank if available
    if (window.MemoryBank) {
      const savedProgress = window.MemoryBank.get('userProgress', {
        lastLogin: null,
        loginStreak: 0,
        viewedEvents: [],
        attendedEvents: []
      });
      
      // Check if this is a new day login
      const today = new Date().toDateString();
      const lastLogin = savedProgress.lastLogin;
      
      if (lastLogin !== today) {
        // It's a new day login
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const yesterdayString = yesterday.toDateString();
        
        // Increment streak if last login was yesterday, otherwise reset
        if (lastLogin === yesterdayString) {
          savedProgress.loginStreak += 1;
        } else if (lastLogin !== null) {
          savedProgress.loginStreak = 1;
        } else {
          // First time login
          savedProgress.loginStreak = 1;
        }
        
        savedProgress.lastLogin = today;
        
        // Save updated progress
        window.MemoryBank.set('userProgress', savedProgress);
      }
      
      // Update with attended events from props
      const attendedEventIds = recentAttendances
        .filter(a => a.status === 'present')
        .map(a => a.event?.id)
        .filter(id => id);
        
      if (attendedEventIds.length > 0) {
        // Add any new attended events
        const newAttendedEvents = [...new Set([...savedProgress.attendedEvents, ...attendedEventIds])];
        if (newAttendedEvents.length !== savedProgress.attendedEvents.length) {
          savedProgress.attendedEvents = newAttendedEvents;
          window.MemoryBank.set('userProgress', savedProgress);
        }
      }
      
      setProgress(savedProgress);
    }
  }, [recentAttendances]);
  
  // Track when user views next event details
  const handleViewEventDetails = () => {
    if (window.MemoryBank && nextEvent) {
      const updatedProgress = {...progress};
      
      // Add to viewed events if not already there
      if (!updatedProgress.viewedEvents.includes(nextEvent.id)) {
        updatedProgress.viewedEvents.push(nextEvent.id);
        setProgress(updatedProgress);
        window.MemoryBank.set('userProgress', updatedProgress);
      }
    }
  };
  
  return (
    <div className="space-y-6">
      {/* Progress Card */}
      <div className="bg-white rounded-lg shadow p-4">
        <h3 className="text-lg font-bold mb-2">Your Progress</h3>
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm text-gray-600">Login Streak</p>
            <p className="text-2xl font-bold">{progress.loginStreak} day{progress.loginStreak !== 1 ? 's' : ''}</p>
          </div>
          <div>
            <p className="text-sm text-gray-600">Events Viewed</p>
            <p className="text-2xl font-bold">{progress.viewedEvents.length}</p>
          </div>
          <div>
            <p className="text-sm text-gray-600">Events Attended</p>
            <p className="text-2xl font-bold">{progress.attendedEvents.length}</p>
          </div>
        </div>
      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2">
          {/* Attendance Stats Card */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Attendance Summary</h3>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div className="text-center p-4 bg-green-50 rounded-lg">
                <p className="text-sm font-medium text-green-600">Present</p>
                <p className="mt-1 text-3xl font-semibold text-green-800">{presentCount}</p>
              </div>
              <div className="text-center p-4 bg-red-50 rounded-lg">
                <p className="text-sm font-medium text-red-600">Absent</p>
                <p className="mt-1 text-3xl font-semibold text-red-800">{absentCount}</p>
              </div>
              <div className="text-center p-4 bg-yellow-50 rounded-lg">
                <p className="text-sm font-medium text-yellow-600">Excused</p>
                <p className="mt-1 text-3xl font-semibold text-yellow-800">{excusedCount}</p>
              </div>
              <div className="text-center p-4 bg-blue-50 rounded-lg">
                <p className="text-sm font-medium text-blue-600">Attendance Rate</p>
                <p className="mt-1 text-3xl font-semibold text-blue-800">{attendanceRate}%</p>
              </div>
            </div>
            <div className="mt-4">
              <div className="w-full bg-gray-200 rounded-full h-2.5">
                <div 
                  className="bg-green-600 h-2.5 rounded-full" 
                  style={{ width: `${attendanceRate}%` }}
                ></div>
              </div>
              <p className="mt-2 text-sm text-gray-600">
                Total Events: {totalEvents}
              </p>
            </div>
          </div>
        </div>
        
        <div>
          {/* Member Status Card */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Member Status</h3>
            <div className="text-center py-6">
              <p className="text-2xl font-bold text-red-700">Active Member</p>
              <p className="text-gray-500 mt-2">Keep up the good work!</p>
              <p className="text-gray-500">Login streak: {progress.loginStreak} days</p>
            </div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
          {/* Next Event Card */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Next Event</h3>
            {nextEvent ? (
              <div className="space-y-4">
                <div>
                  <h3 className="text-xl font-bold text-gray-900">{nextEvent.name}</h3>
                  <p className="text-gray-600">{nextEvent.description}</p>
                </div>
                <div className="flex items-center">
                  <svg className="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span className="text-gray-700">
                    {new Date(nextEvent.date).toLocaleDateString('en-US', {
                      weekday: 'long',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric'
                    })}
                  </span>
                </div>
                {nextEvent.time && (
                  <div className="flex items-center">
                    <svg className="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span className="text-gray-700">{nextEvent.time}</span>
                  </div>
                )}
                {nextEvent.location && (
                  <div className="flex items-center">
                    <svg className="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span className="text-gray-700">{nextEvent.location}</span>
                  </div>
                )}
                <div className="flex justify-center mt-2">
                  <button
                    onClick={handleViewEventDetails}
                    className="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                  >
                    View Details
                  </button>
                </div>
              </div>
            ) : (
              <div className="text-center py-6">
                <p className="text-gray-500">No upcoming events scheduled</p>
              </div>
            )}
          </div>
        </div>
        
        <div className="lg:col-span-2">
          {/* Recent Attendances Card */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Recent Attendance</h3>
            {recentAttendances && recentAttendances.length > 0 ? (
              <>
                <div className="overflow-hidden">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr>
                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Event
                        </th>
                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Date
                        </th>
                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Status
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                      {recentAttendances.map((attendance, index) => {
                        let statusClass = 'bg-gray-100 text-gray-800';
                        if (attendance.status === 'present') statusClass = 'bg-green-100 text-green-800';
                        else if (attendance.status === 'absent') statusClass = 'bg-red-100 text-red-800';
                        else if (attendance.status === 'excused') statusClass = 'bg-yellow-100 text-yellow-800';
                        
                        return (
                          <tr key={index}>
                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                              {attendance.event?.name || 'Unknown Event'}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {attendance.event?.date ? new Date(attendance.event.date).toLocaleDateString() : 'N/A'}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm">
                              <span className={`px-2 py-1 text-xs rounded-full ${statusClass}`}>
                                {attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1)}
                              </span>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
                <div className="mt-4 flex justify-end">
                  <a href="/member/attendances" className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    View All
                  </a>
                </div>
              </>
            ) : (
              <div className="text-center py-6">
                <p className="text-gray-500">No attendance records found</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default MemberDashboard; 