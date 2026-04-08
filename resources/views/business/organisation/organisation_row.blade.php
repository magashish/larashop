@php
$row_id = (string) Str::uuid(); 
@endphp
<tr class="{{ $organisation->id }}">
    <td>{{ $organisation->title ?? '' }} </td>
    <td>business</td>
    <td> {{ $pivotData['start_date'] }} </td>
    <td> </td>
    <td></td>
</tr>
