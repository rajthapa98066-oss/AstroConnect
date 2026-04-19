<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
@php
    $allowedImages = config('chatify.attachments.allowed_images');
    $allowedFiles = config('chatify.attachments.allowed_files');
    $allAllowedExtensions = array_values(array_merge($allowedImages, $allowedFiles));
    $chatifyConfig = [
        'name' => config('chatify.name'),
        'sounds' => config('chatify.sounds'),
        'allowedImages' => $allowedImages,
        'allowedFiles' => $allowedFiles,
        'allAllowedExtensions' => $allAllowedExtensions,
        'maxUploadSize' => Chatify::getMaxUploadSize(),
        'pusher' => config('chatify.pusher'),
        'pusherAuthEndpoint' => route('pusher.auth'),
    ];
@endphp
<script >
    // Gloabl Chatify variables from PHP to JS
    window.chatify = @json($chatifyConfig);
</script>
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
