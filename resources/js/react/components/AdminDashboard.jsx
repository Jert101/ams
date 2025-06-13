import React, { useState, useEffect } from 'react';

/**
 * Admin Dashboard component
 * Displays key metrics, recent users, and quick actions
 */
const AdminDashboard = ({
  totalUsers = 0,
  totalEvents = 0,
  totalAttendances = 0,
  totalNotifications = 0,
  recentUsers = [],
  recentAttendances = []
}) => {
  // State for memory bank contents
  const [memoryBankStats, setMemoryBankStats] = useState({
    totalEntries: 0,
    userProgressEntries: 0,
    systemEntries: 0
  });
  
  // Load memory bank stats
  useEffect(() => {
    if (window.MemoryBank) {
      try {
        // Count entries
        let totalEntries = 0;
        let userProgressEntries = 0;
        let systemEntries = 0;
        
        Object.keys(localStorage).forEach(key => {
          if (key.startsWith('ams_')) {
            totalEntries++;
            if (key.includes('userProgress')) {
              userProgressEntries++;
            } else {
              systemEntries++;
            }
          }
        });
        
        setMemoryBankStats({
          totalEntries,
          userProgressEntries,
          systemEntries
        });
      } catch (e) {
        console.error('Error loading memory bank stats:', e);
      }
    }
  }, []);
  
  // Clear all memory bank data
  const handleClearMemoryBank = () => {
    if (window.MemoryBank && confirm('Are you sure you want to clear all saved data? This cannot be undone.')) {
      window.MemoryBank.clear();
      setMemoryBankStats({
        totalEntries: 0,
        userProgressEntries: 0,
        systemEntries: 0
      });
      alert('Memory bank cleared successfully.');
    }
  };
  
  // Helper function to get the profile photo URL
  const getProfilePhotoUrl = (user) => {
    // First check if the profile_photo_url is already provided
    if (user.profile_photo_url) {
      return user.profile_photo_url;
    }
    
    // Check if user has a profile photo path
    if (user.profile_photo_path) {
      // Check if it's the default logo
      if (user.profile_photo_path === 'kofa.png') {
        return '/img/kofa.png';
      }
      
      // Get the filename from the path
      const filename = user.profile_photo_path.split('/').pop();
      
      // Add cache busting parameter
      const cacheBuster = `?v=${Date.now()}`;
      
      // Try different paths
      // First check if it's a full path
      if (user.profile_photo_path.startsWith('/')) {
        return `${user.profile_photo_path}${cacheBuster}`;
      }
      
      // Check for storage path
      if (user.profile_photo_path.includes('profile-photos/')) {
        return `/storage/${user.profile_photo_path}${cacheBuster}`;
      }
      
      // Try direct path
      return `/profile-photos/${filename}${cacheBuster}`;
    }
    
    // Default fallback
    return '/img/kofa.png';
  };
  
  return (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Total Users</p>
              <p className="text-2xl font-semibold text-gray-900">{totalUsers}</p>
            </div>
          </div>
        </div>
        
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="p-3 rounded-full bg-green-100 text-green-600 mr-4">
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
            <div className="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
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
            <div className="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Notifications</p>
              <p className="text-2xl font-semibold text-gray-900">{totalNotifications}</p>
            </div>
          </div>
        </div>
      </div>
      
      {/* Memory Bank Stats */}
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-lg font-bold mb-4">Memory Bank Status</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div className="p-4 bg-blue-50 rounded-lg text-center">
            <p className="text-sm font-medium text-blue-700">Total Entries</p>
            <p className="text-2xl font-bold text-blue-800">{memoryBankStats.totalEntries}</p>
          </div>
          <div className="p-4 bg-green-50 rounded-lg text-center">
            <p className="text-sm font-medium text-green-700">User Progress Entries</p>
            <p className="text-2xl font-bold text-green-800">{memoryBankStats.userProgressEntries}</p>
          </div>
          <div className="p-4 bg-yellow-50 rounded-lg text-center">
            <p className="text-sm font-medium text-yellow-700">System Entries</p>
            <p className="text-2xl font-bold text-yellow-800">{memoryBankStats.systemEntries}</p>
          </div>
        </div>
        <div className="flex justify-end">
          <button 
            onClick={handleClearMemoryBank}
            className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Clear All Data
          </button>
        </div>
      </div>
      
      {/* Quick Actions and User List */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-bold mb-4">Quick Actions</h3>
            <div className="grid grid-cols-1 gap-3">
              <a href="/admin/users/create" className="flex items-center justify-center px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Add New User
              </a>
              
              <a href="/admin/events/create" className="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Create New Event
              </a>
              
              <a href="/admin/reports" className="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Reports
              </a>
            </div>
          </div>
          
          <div className="bg-white rounded-lg shadow p-6 mt-6">
            <h3 className="text-lg font-bold mb-4">Attendance Statistics</h3>
            <div className="grid grid-cols-3 gap-4">
              <div className="text-center p-4 bg-green-50 rounded-lg">
                <p className="text-sm font-medium text-green-600">Present</p>
                <p className="mt-1 text-3xl font-semibold text-green-800">
                  {recentAttendances.filter(a => a.status === 'present').length}
                </p>
              </div>
              <div className="text-center p-4 bg-red-50 rounded-lg">
                <p className="text-sm font-medium text-red-600">Absent</p>
                <p className="mt-1 text-3xl font-semibold text-red-800">
                  {recentAttendances.filter(a => a.status === 'absent').length}
                </p>
              </div>
              <div className="text-center p-4 bg-yellow-50 rounded-lg">
                <p className="text-sm font-medium text-yellow-600">Excused</p>
                <p className="mt-1 text-3xl font-semibold text-yellow-800">
                  {recentAttendances.filter(a => a.status === 'excused').length}
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div className="lg:col-span-2">
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-bold">Recent Users</h3>
              <a href="/admin/users" className="text-sm text-blue-600 hover:text-blue-500">
                View All
              </a>
            </div>
            
            {recentUsers && recentUsers.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 table-responsive">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {recentUsers.map((user, index) => (
                      <tr key={index}>
                        <td className="px-6 py-4 whitespace-nowrap" data-label="Name">
                          <div className="flex items-center">
                            <div className="flex-shrink-0 h-10 w-10">
                              <img 
                                className="h-10 w-10 rounded-full object-cover" 
                                src={user.profile_photo_url || getProfilePhotoUrl(user)} 
                                alt={user.name}
                                onError={(e) => {
                                  e.target.onerror = null;
                                  e.target.src = '/img/kofa.png';
                                }}
                              />
                            </div>
                            <div className="ml-4">
                              <div className="text-sm font-medium text-gray-900">{user.name}</div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap" data-label="Email">
                          <div className="text-sm text-gray-900">{user.email}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap" data-label="Role">
                          <div className="text-sm text-gray-900">{user.role?.name || 'No Role'}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap" data-label="Status">
                          {user.approval_status === 'approved' ? (
                            <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Approved
                            </span>
                          ) : (
                            <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                              Pending
                            </span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="text-center py-6">
                <p className="text-gray-500">No users found</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard; 