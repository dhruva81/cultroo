@push('styles')
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
      <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">

@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
      flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            wrap: true,
            });
</script>
@endpush


{{-- <div class="flex flatpickr">
      <input type="text"  placeholder="Select Date.." data-input> <!-- input is mandatory -->
      <a class="input-button" title="toggle" data-toggle>
            <x-heroicon-o-calendar class="h-5 w-5" />
        </a>
      <a class="input-button" title="clear" data-clear>
          <x-heroicon-o-x class="h-5 w-5" />
      </a>
  </div> --}}
