/* cursor-pizza.js — Cursor personalizado para Tiempo Delivery */
(function () {
    if (!matchMedia('(pointer:fine)').matches) return;
    if (matchMedia('(prefers-reduced-motion:reduce)').matches) return;

    const cursor = document.getElementById('pizzaCursor');
    if (!cursor) return;

    document.body.classList.add('has-pizza-cursor');

    let mx = -999, my = -999, cx = -999, cy = -999;

    document.addEventListener('mousemove', function (e) {
        mx = e.clientX;
        my = e.clientY;
    });

    function loop() {
        cx += (mx - cx) * 0.14;
        cy += (my - cy) * 0.14;
        cursor.style.transform = 'translate(' + (cx - 22) + 'px, ' + (cy - 22) + 'px)';
        requestAnimationFrame(loop);
    }
    loop();
})();
