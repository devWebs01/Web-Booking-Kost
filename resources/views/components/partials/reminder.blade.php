<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        text-align: center;
    }
</style>

@if (now() >= Carbon\carbon::parse("2025-07-30"))
    <div id="overlay" class="overlay">
        <p class="fw-bold text-white">Maaf, tombol dan layar tidak dapat diakses saat ini. <br><a
                href="https://wa.me/6282280514945">Hubungi
                kami</a> untuk
            perpanjang masa aktif website ini.</p>
    </div>
@endif
