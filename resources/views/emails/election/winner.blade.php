@component('mail::message')
# KOFA Election Results

We are pleased to announce the winners of the recent KOFA election:

@foreach($positions as $position)
## {{ $position['title'] }}
@foreach($position['winners'] as $winner)
- **{{ $winner['name'] }}**
  - Votes Received: {{ $winner['votes'] }}
  - Percentage: {{ number_format($winner['percentage'], 2) }}%
@endforeach

@endforeach

## Next Steps
1. All winners will be contacted by the administration team regarding the formal turnover process.
2. Winners should prepare necessary documentation for their new roles.
3. A formal induction ceremony will be scheduled soon.

Thank you to all candidates and voters for participating in this democratic process.

Best regards,<br>
{{ config('app.name') }} Election Committee
@endcomponent 