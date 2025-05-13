<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
    protected $firebase;
    protected $firestore;
    protected $database;
    
    public function __construct()
    {
        $this->initializeFirebase();
    }
    
    /**
     * Initialize Firebase services
     */
    private function initializeFirebase()
    {
        try {
            $serviceAccountPath = config('firebase.credentials_path');
            
            $factory = (new Factory)
                ->withServiceAccount($serviceAccountPath)
                ->withDatabaseUri(config('firebase.database_url'));
                
            $this->firebase = $factory->createFirebase();
            $this->firestore = $factory->createFirestore()->database();
            $this->database = $factory->createDatabase();
        } catch (\Exception $e) {
            logger()->error('Firebase initialization error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the Firestore database instance
     */
    public function getFirestore()
    {
        return $this->firestore;
    }
    
    /**
     * Get the Realtime Database instance
     */
    public function getDatabase()
    {
        return $this->database;
    }
    
    /**
     * Sync attendance data with Firebase
     */
    public function syncAttendance($attendance)
    {
        try {
            $attendanceData = [
                'id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'event_id' => $attendance->event_id,
                'status' => $attendance->status,
                'approved_by' => $attendance->approved_by,
                'approved_at' => $attendance->approved_at ? $attendance->approved_at->format('Y-m-d H:i:s') : null,
                'remarks' => $attendance->remarks,
                'created_at' => $attendance->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $attendance->updated_at->format('Y-m-d H:i:s'),
            ];
            
            // Add to Firestore
            $this->firestore->collection('attendances')->document((string)$attendance->id)->set($attendanceData);
            
            // Also update in Realtime Database for faster queries
            $this->database->getReference('attendances/' . $attendance->id)->set($attendanceData);
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Firebase sync error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sync event data with Firebase
     */
    public function syncEvent($event)
    {
        try {
            $eventData = [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->date->format('Y-m-d'),
                'time' => $event->time ? $event->time->format('H:i:s') : null,
                'description' => $event->description,
                'is_active' => $event->is_active,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s'),
            ];
            
            // Add to Firestore
            $this->firestore->collection('events')->document((string)$event->id)->set($eventData);
            
            // Also update in Realtime Database
            $this->database->getReference('events/' . $event->id)->set($eventData);
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Firebase sync error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get attendance records from Firebase
     */
    public function getAttendances($filters = [])
    {
        try {
            $query = $this->firestore->collection('attendances');
            
            // Apply filters
            if (isset($filters['user_id'])) {
                $query = $query->where('user_id', '=', $filters['user_id']);
            }
            
            if (isset($filters['event_id'])) {
                $query = $query->where('event_id', '=', $filters['event_id']);
            }
            
            if (isset($filters['status'])) {
                $query = $query->where('status', '=', $filters['status']);
            }
            
            $snapshot = $query->documents();
            $attendances = [];
            
            foreach ($snapshot as $document) {
                $attendances[] = $document->data();
            }
            
            return $attendances;
        } catch (\Exception $e) {
            logger()->error('Firebase query error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get events from Firebase
     */
    public function getEvents($filters = [])
    {
        try {
            $query = $this->firestore->collection('events');
            
            // Apply filters
            if (isset($filters['is_active'])) {
                $query = $query->where('is_active', '=', $filters['is_active']);
            }
            
            if (isset($filters['date_from'])) {
                $query = $query->where('date', '>=', $filters['date_from']);
            }
            
            if (isset($filters['date_to'])) {
                $query = $query->where('date', '<=', $filters['date_to']);
            }
            
            $snapshot = $query->documents();
            $events = [];
            
            foreach ($snapshot as $document) {
                $events[] = $document->data();
            }
            
            return $events;
        } catch (\Exception $e) {
            logger()->error('Firebase query error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Record attendance in real-time
     */
    public function recordAttendance($userId, $eventId, $status = 'present', $approvedBy = null)
    {
        try {
            $attendanceData = [
                'user_id' => $userId,
                'event_id' => $eventId,
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => now()->format('Y-m-d H:i:s'),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];
            
            // Generate a unique ID for the attendance record
            $newAttendanceRef = $this->database->getReference('attendances')->push();
            $newId = $newAttendanceRef->getKey();
            
            $attendanceData['id'] = $newId;
            
            // Save to Firestore
            $this->firestore->collection('attendances')->document($newId)->set($attendanceData);
            
            // Save to Realtime Database
            $newAttendanceRef->set($attendanceData);
            
            return $attendanceData;
        } catch (\Exception $e) {
            logger()->error('Firebase attendance recording error: ' . $e->getMessage());
            return null;
        }
    }
}
