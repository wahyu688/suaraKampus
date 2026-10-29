@props(['status'])

<span class="sk-badge sk-badge-{{ $status }}">
    {{ \App\Models\Report::STATUS_LABELS[$status] }}
</span>
