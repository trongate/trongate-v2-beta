<div class="center-stage cloak">
    <div class="mt-1">Enter Module Name</div>
    <form action="#" id="enter-mod-name-form"
                mx-post="desktop_app_api/submit_mod_name"
                mx-target="main" mx-after-swap="TrongateCodeGenerator.handleApiResponse"
                mx-target-loading="cloak"
                class="highlight-errors">
        <div class="mt-1">
            <input type="text" name="mod_name" autocomplete="off">
        </div>
        <div class="mt-1">
            <button>Submit</button>
        </div>
    </form>
</div>
