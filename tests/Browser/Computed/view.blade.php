<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<div x-data="@radio(\Radio\Tests\Browser\Computed\Component::class)">
    <p x-text="message"></p>

    <button x-on:click.prevent="increment()" dusk="increment">
        Change Message
    </button>
</div>

@radioScripts
