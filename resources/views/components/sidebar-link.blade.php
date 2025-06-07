@props(['active'])

@php
$role = auth()->user()->role->name ?? 'guest';
$roleLower = strtolower($role);

// Define role-specific active colors
$roleColors = [
    'admin' => 'bg-red-100 text-red-700',
    'officer' => 'bg-cyan-100 text-cyan-700',
    'secretary' => 'bg-indigo-100 text-indigo-700',
    'member' => 'bg-green-100 text-green-700',
    'guest' => 'bg-gray-100 text-gray-700'
];

// Define role-specific hover colors
$roleHoverColors = [
    'admin' => 'hover:bg-red-50 hover:text-red-700',
    'officer' => 'hover:bg-cyan-50 hover:text-cyan-700',
    'secretary' => 'hover:bg-indigo-50 hover:text-indigo-700',
    'member' => 'hover:bg-green-50 hover:text-green-700',
    'guest' => 'hover:bg-gray-50 hover:text-gray-700'
];

// Get the appropriate colors based on user role
$activeColors = $roleColors[$roleLower] ?? $roleColors['guest'];
$hoverColors = $roleHoverColors[$roleLower] ?? $roleHoverColors['guest'];

// Define classes based on active state
$classes = ($active ?? false)
    ? 'flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link active ' . $activeColors
    : 'flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md transition-colors duration-200 sidebar-link ' . $hoverColors;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a> 