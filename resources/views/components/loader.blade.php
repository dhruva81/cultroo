<style>
    .loader {
      border-top-color: #3498db;
      -webkit-animation: spinner 1.5s linear infinite;
      animation: spinner 1.5s linear infinite;
    }

    @-webkit-keyframes spinner {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spinner {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>
<div
    wire:loading
    {{ $attributes->merge(['class'=> 'mt-2 mx-2 loader ease-linear rounded-full border-2 border-t-2 border-green-200 h-4 w-4']) }}
    >
</div>

