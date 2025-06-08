<?php
// This script allows direct rejection of candidates when the modal doesn't work

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

// Load Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Direct Candidate Rejection Tool</h1>";

// Process rejection if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'])) {
    $candidateId = (int)$_POST['candidate_id'];
    $reason = $_POST['reason'] ?? 'Rejected via direct tool';
    
    try {
        // Get candidate details first
        $candidate = DB::table('election_candidates')->where('id', $candidateId)->first();
        
        if (!$candidate) {
            echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
            echo "Candidate with ID $candidateId not found!";
            echo "</div>";
        } else {
            // Update the candidate status
            DB::table('election_candidates')
                ->where('id', $candidateId)
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'updated_at' => now()
                ]);
            
            echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
            echo "✅ Candidate application has been successfully rejected!";
            echo "</div>";
            
            // Show details of updated candidate
            $updatedCandidate = DB::table('election_candidates')->where('id', $candidateId)->first();
            
            echo "<div style='margin: 20px 0; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "<h3>Updated Candidate Details</h3>";
            echo "<p><strong>ID:</strong> " . $updatedCandidate->id . "</p>";
            echo "<p><strong>Status:</strong> " . $updatedCandidate->status . "</p>";
            echo "<p><strong>Rejection Reason:</strong> " . $updatedCandidate->rejection_reason . "</p>";
            echo "<p><strong>Updated At:</strong> " . $updatedCandidate->updated_at . "</p>";
            echo "</div>";
        }
    } catch (\Exception $e) {
        echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "Error: " . $e->getMessage();
        echo "</div>";
    }
}

// Get pending candidates for the form
try {
    $pendingCandidates = DB::table('election_candidates as ec')
        ->join('users as u', 'ec.user_id', '=', 'u.id')
        ->join('election_positions as ep', 'ec.position_id', '=', 'ep.id')
        ->where('ec.status', 'pending')
        ->select('ec.id', 'u.name as user_name', 'ep.title as position_title')
        ->get();
    
    echo "<form method='post' style='margin: 20px 0; padding: 20px; background-color: #f0f8ff; border: 1px solid #bed4e6; border-radius: 8px;'>";
    echo "<h2>Reject Candidate Application</h2>";
    
    if (count($pendingCandidates) > 0) {
        echo "<div class='form-group' style='margin-bottom: 15px;'>";
        echo "<label for='candidate_id' style='display: block; margin-bottom: 5px; font-weight: bold;'>Select Candidate:</label>";
        echo "<select name='candidate_id' id='candidate_id' required style='width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;'>";
        echo "<option value=''>-- Select a candidate --</option>";
        
        foreach ($pendingCandidates as $candidate) {
            echo "<option value='" . $candidate->id . "'>" . $candidate->user_name . " - " . $candidate->position_title . " (ID: " . $candidate->id . ")</option>";
        }
        
        echo "</select>";
        echo "</div>";
        
        echo "<div class='form-group' style='margin-bottom: 15px;'>";
        echo "<label for='reason' style='display: block; margin-bottom: 5px; font-weight: bold;'>Rejection Reason:</label>";
        echo "<textarea name='reason' id='reason' rows='3' required style='width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;'></textarea>";
        echo "</div>";
        
        echo "<button type='submit' style='padding: 8px 16px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;'>Reject Candidate</button>";
    } else {
        echo "<div style='padding: 15px; background-color: #e9ecef; border-radius: 4px;'>";
        echo "No pending candidates found.";
        echo "</div>";
    }
    
    echo "</form>";
    
    // List all candidates
    echo "<div style='margin: 20px 0; padding: 20px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;'>";
    echo "<h2>All Candidates</h2>";
    
    $allCandidates = DB::table('election_candidates as ec')
        ->leftJoin('users as u', 'ec.user_id', '=', 'u.id')
        ->leftJoin('election_positions as ep', 'ec.position_id', '=', 'ep.id')
        ->select('ec.id', 'u.name as user_name', 'ep.title as position_title', 'ec.status', 'ec.rejection_reason', 'ec.created_at')
        ->get();
    
    if (count($allCandidates) > 0) {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<thead style='background-color: #f0f0f0;'>";
        echo "<tr>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Name</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Position</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Status</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Rejection Reason</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Created At</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($allCandidates as $candidate) {
            $statusColor = $candidate->status === 'approved' ? '#d4edda' : 
                           ($candidate->status === 'pending' ? '#fff3cd' : '#f8d7da');
            
            echo "<tr style='background-color: " . $statusColor . ";'>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $candidate->id . "</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($candidate->user_name ?? 'Unknown') . "</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($candidate->position_title ?? 'Unknown Position') . "</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ucfirst($candidate->status) . "</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($candidate->rejection_reason ?? '-') . "</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $candidate->created_at . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div style='padding: 15px; background-color: #e9ecef; border-radius: 4px;'>";
        echo "No candidates found.";
        echo "</div>";
    }
    
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
    echo "Error loading candidates: " . $e->getMessage();
    echo "</div>";
}

// Navigation
echo "<div style='margin-top: 20px;'>";
echo "<a href='/admin/election/candidates' style='display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Return to Candidates</a>";
echo "<a href='/admin/election' style='display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;'>Election Management</a>";
echo "</div>"; 