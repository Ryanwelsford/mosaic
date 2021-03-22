<section>
    <button title="Scroll to top" onclick="scrollUpTop()" class="ph-button ph-button-important top-button">Top</button>
</section>
<script>
    window.onscroll = function() {
        scrollCheck();
    };

    function scrollCheck() {
        let button = document.querySelector(".top-button");
        if (window.scrollY > 250) {
            button.style.display = "block";
        }
        else {
            button.style.display = "none";
        }
    }
</script>
