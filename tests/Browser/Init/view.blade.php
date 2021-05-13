<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<div x-data="@radio(\Radio\Tests\Browser\Init\Component::class)">
    <p x-text="message"></p>

    <button x-on:click.prevent="changeMessage()" dusk="change-message">
        Change Message
    </button>
</div>

@radioScripts
