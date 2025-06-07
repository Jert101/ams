import React, { useState, useEffect } from 'react';

/**
 * Officer Dashboard component
 * Displays event statistics, attendances, and quick actions for officers
 */
const OfficerDashboard = ({
  totalEvents = 0,
  totalAttendances = 0,
  todayAttendanceCount = 0,
  pendingVerificationsCount = 0,
  upcomingEvents = [],
  todayEvent = null,
  recentAttendances = []
}) => {
  // State for scan history
  const [scanHistory, setScanHistory] = useState({
    totalScans: 0,
    lastScanTime: null,
    recentScans: []
  });
  
  // Load scan history from memory bank
  useEffect(() => {
    if (window.MemoryBank) {
      const history = window.MemoryBank.get('officerScanHistory', {
        totalScans: 0,
        lastScanTime: null,
        recentScans: []
      });
      
      setScanHistory(history);
    }
  }, []);
  
  // Mock function to simulate a new scan (would be called by QR code scanner)
  const mockRecordScan = () => {
    if (window.MemoryBank) {
      const newScan = {
        code: 'MEMBER' + Math.floor(Math.random() * 1000),
        timestamp: new Date().toISOString(),
        eventId: todayEvent ? todayEvent.id : null
      };
      
      // Update scan history
      const updatedHistory = { ...scanHistory };
      updatedHistory.totalScans += 1;
      updatedHistory.lastScanTime = new Date().toISOString();
      
      // Add to recent scans (keep last 5)
      updatedHistory.recentScans.unshift(newScan);
      if (updatedHistory.recentScans.length > 5) {
        updatedHistory.recentScans = updatedHistory.recentScans.slice(0, 5);
      }
      
      // Save to memory bank
      window.MemoryBank.set('officerScanHistory', updatedHistory);
      
      // Update state
      setScanHistory(updatedHistory);
      
      alert('New scan recorded: ' + newScan.code);
    }
  };
  
  return (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Total Events</p>
              <p className="text-2xl font-semibold text-gray-900">{totalEvents}</p>
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-green-100 text-green-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Total Attendances</p>
              <p className="text-2xl font-semibold text-gray-900">{totalAttendances}</p>
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-green-100 text-green-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Present Today</p>
              <p className="text-2xl font-semibold text-gray-900">{todayAttendanceCount}</p>
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Pending Verifications</p>
              <p className="text-2xl font-semibold text-gray-900">{pendingVerificationsCount}</p>
            </div>
          </div>
        </div>
      </div>
      
      {/* Scan History Card */}
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-lg font-bold mb-4">QR Code Scan History</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div className="p-4 bg-blue-50 rounded-lg text-center">
            <p className="text-sm font-medium text-blue-700">Total Scans</p>
            <p className="text-2xl font-bold text-blue-800">{scanHistory.totalScans}</p>
          </div>
          <div className="p-4 bg-green-50 rounded-lg text-center">
            <p className="text-sm font-medium text-green-700">Last Scan</p>
            <p className="text-md font-bold text-green-800">
              {scanHistory.lastScanTime 
                ? new Date(scanHistory.lastScanTime).toLocaleString() 
                : 'No scans yet'}
            </p>
          </div>
          <div className="p-4 bg-yellow-50 rounded-lg text-center">
            <button 
              onClick={mockRecordScan}
              className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Record Test Scan
            </button>
          </div>
        </div>
        
        {scanHistory.recentScans.length > 0 && (
          <div className="mt-4">
            <h4 className="font-medium mb-2">Recent Scans</h4>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Code
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Timestamp
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Event
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {scanHistory.recentScans.map((scan, index) => (
                    <tr key={index}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {scan.code}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(scan.timestamp).toLocaleString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {scan.eventId ? `Event #${scan.eventId}` : 'N/A'}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
      
      {/* Main Content */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
          {/* Quick Actions */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Quick Actions</h3>
            <div className="grid grid-cols-1 gap-3">
              <a href="/officer/scan" className="flex items-center justify-center px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Scan QR Code
              </a>
              
              <a href="/officer/attendances/pending" className="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verify Attendances
              </a>
              
              <a href="/officer/events/create" className="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Event
              </a>
            </div>
          </div>
          
          {/* Upcoming Events */}
          {upcomingEvents && upcomingEvents.length > 0 && (
            <div className="bg-white rounded-lg shadow p-6 mt-6">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-bold">Upcoming Events</h3>
                <a href="/officer/events" className="text-sm text-blue-600 hover:text-blue-900">
                  View All
                </a>
              </div>
              <div className="space-y-4">
                {upcomingEvents.map((event, index) => (
                  <div key={index} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div className="flex justify-between items-start">
                      <div>
                        <h3 className="font-medium text-gray-900">{event.name}</h3>
                        <p className="text-sm text-gray-500">
                          {new Date(event.date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                          })}
                        </p>
                        {event.location && (
                          <p className="text-sm text-gray-500 mt-1">{event.location}</p>
                        )}
                      </div>
                      <span className={`px-2 py-1 text-xs font-semibold rounded-full ${event.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                        {event.is_active ? 'Active' : 'Inactive'}
                      </span>
                    </div>
                    <div className="mt-3 flex justify-end">
                      <a href={`/officer/events/${event.id}`} className="text-sm text-blue-600 hover:text-blue-900">
                        View Details
                      </a>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
        
        <div className="lg:col-span-2">
          {/* Current Event */}
          {todayEvent && (
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-bold mb-4">Current Event</h3>
              <div className="space-y-3">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900">{todayEvent.name}</h3>
                  {todayEvent.description && (
                    <p className="text-sm text-gray-500">{todayEvent.description}</p>
                  )}
                </div>
                <div className="flex items-center">
                  <svg className="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span className="text-gray-700">
                    {new Date(todayEvent.date).toLocaleDateString('en-US', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric'
                    })}
                  </span>
                </div>
                {todayEvent.location && (
                  <div className="flex items-center">
                    <svg className="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span className="text-gray-700">{todayEvent.location}</span>
                  </div>
                )}
              </div>
              
              <div className="mt-6 grid grid-cols-3 gap-4 text-center">
                <div className="p-3 bg-green-50 rounded-lg">
                  <p className="text-3xl font-bold text-green-600">
                    {todayEvent.attendanceStats?.present || 0}
                  </p>
                  <p className="text-green-700 font-medium">Present</p>
                </div>
                <div className="p-3 bg-red-50 rounded-lg">
                  <p className="text-3xl font-bold text-red-600">
                    {todayEvent.attendanceStats?.absent || 0}
                  </p>
                  <p className="text-red-700 font-medium">Absent</p>
                </div>
                <div className="p-3 bg-yellow-50 rounded-lg">
                  <p className="text-3xl font-bold text-yellow-600">
                    {todayEvent.attendanceStats?.excused || 0}
                  </p>
                  <p className="text-yellow-700 font-medium">Excused</p>
                </div>
              </div>
              
              <div className="mt-4 flex justify-center">
                <a href={`/officer/events/${todayEvent.id}/attendances`} className="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                  View Attendances
                </a>
              </div>
            </div>
          )}
          
          {/* Recent Attendances */}
          <div className={`bg-white rounded-lg shadow p-6 ${todayEvent ? 'mt-6' : ''}`}>
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-bold">Recent Attendances</h3>
              <a href="/officer/attendances" className="text-sm text-blue-600 hover:text-blue-900">
                View All
              </a>
            </div>
            
            {recentAttendances && recentAttendances.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Member
                      </th>
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
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm font-medium text-gray-900">
                              {attendance.user?.name || 'Unknown'}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-900">
                              {attendance.event?.name || 'Unknown Event'}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-500">
                              {new Date(attendance.created_at).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric'
                              })}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
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
            ) : (
              <div className="text-center py-6">
                <p className="text-gray-500">No recent attendances found</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default OfficerDashboard; 