<div class="log-viewer h-full overflow-y-auto">
    <div class="p-4">
        <h3 class="font-bold mb-2">Building Data Logs</h3>
        <div class="bg-gray-100 p-2 rounded">
            <pre class="text-xs font-mono whitespace-pre-wrap">@foreach($logs as $log){!! $log !!}
            @endforeach</pre>
        </div>
    </div>
</div>