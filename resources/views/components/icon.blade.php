@props(['name', 'class' => 'w-5 h-5'])

@php
    $paths = [
        'home'        => 'M3 11.5 12 4l9 7.5M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9',
        'calendar'    => 'M8 2v3M16 2v3M3.5 9h17M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z',
        'image'       => 'M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm0 11 5-5 4 4 3-3 6 6M16 10a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z',
        'wallet'      => 'M4 7a2 2 0 0 1 2-2h11a1 1 0 0 1 1 1v2H6a2 2 0 1 0 0 4h13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Zm12 6h.01',
        'flag'        => 'M5 21V4m0 0h11l-2 4 2 4H5',
        'users'       => 'M16 14a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-8 0a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm0 2c-2.7 0-5 1.5-5 4v1h7m6-5c-3 0-7 1.5-7 4v1h14v-1c0-2.5-4-4-7-4Z',
        'user'        => 'M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-3.3 0-8 1.7-8 5v1h16v-1c0-3.3-4.7-5-8-5Z',
        'plus'        => 'M12 5v14M5 12h14',
        'edit'        => 'M4 20h4l10-10-4-4L4 16v4Zm10-12 4 4',
        'trash'       => 'M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m2 0v12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V7h10Z',
        'close'       => 'M6 6l12 12M18 6 6 18',
        'check'       => 'M5 12l5 5L20 7',
        'check-circle'=> 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'alert'       => 'M12 9v4m0 4h.01M10.3 3.7 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z',
        'info'        => 'M12 11v6m0-10h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'clock'       => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'map-pin'     => 'M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Zm0-9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        'logout'      => 'M9 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4m6-14 5 5-5 5m5-5H9',
        'login'       => 'M15 5h4a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4M9 5l-5 5 5 5m-5-5h12',
        'menu'        => 'M4 6h16M4 12h16M4 18h16',
        'arrow-left'  => 'M15 6l-6 6 6 6',
        'arrow-right' => 'M9 6l6 6-6 6',
        'chevron-down'=> 'M6 9l6 6 6-6',
        'chevron-up'  => 'M6 15l6-6 6 6',
        'search'      => 'M21 21l-4.3-4.3m1.8-5.7a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z',
        'download'    => 'M12 4v12m0 0 4-4m-4 4-4-4M4 20h16',
        'arrow-up'    => 'M12 19V5m0 0-6 6m6-6 6 6',
        'arrow-down'  => 'M12 5v14m0 0 6-6m-6 6-6-6',
        'cash'        => 'M12 7v10m4-5H8m13 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'settings'    => 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm9 4-2-1v-2l-2-2-2 1-2-1-1-2H8L7 7 5 6 3 8v2L1 12l2 1v2l2 2 2-1 2 1 1 2h4l1-2 2-1 2-2v-2l2-1Z',
        'pencil'      => 'M4 20h4l10-10-4-4L4 16v4Z',
        'document'    => 'M9 13h6m-6 4h4m1-13H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z',
        'building'    => 'M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01',
        'megaphone'   => 'M3 11v2a2 2 0 0 0 2 2h2l4 4V5L7 9H5a2 2 0 0 0-2 2Zm14-4v10m4-8v6',
    ];
    $d = $paths[$name] ?? $paths['info'];
@endphp

<svg xmlns="http://www.w3.org/2000/svg"
     fill="none"
     viewBox="0 0 24 24"
     stroke="currentColor"
     stroke-width="1.75"
     stroke-linecap="round"
     stroke-linejoin="round"
     aria-hidden="true"
     {{ $attributes->merge(['class' => $class]) }}>
    <path d="{{ $d }}" />
</svg>
