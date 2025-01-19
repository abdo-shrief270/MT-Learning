<x-filament-panels::page>
    <div class="p-4 space-y-4">
        <h2 class="text-lg font-semibold">Meeting: {{ $meeting->name }} For Lesson: {{ $meeting->lesson->title }} </h2>
        <div class="mt-4">
            <iframe
                src="{{ $meeting->url }}"
                allow="camera; microphone; fullscreen; speaker; display-capture"
                style="width: 100%; height: 80vh; border: 0;"
            ></iframe>
        </div>
    </div>
</x-filament-panels::page>
