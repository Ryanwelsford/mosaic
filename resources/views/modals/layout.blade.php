<section class="modal" id="search-modal">
    <div class="modal-internal small-modal">
        <div class="modal-title"> @yield("modal_title") <button onclick="searchModal()" class="close-X">X</button></div>
        <div class="modal-content vert-center">
            <div class="modal-center">
                @yield("modal_content")
            </div>
        </div>
    </div>
</section>
