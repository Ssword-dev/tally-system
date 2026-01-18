$(function() {
    // navbar logic
    let resizing = false;

    function debouncify(fn, duration){
        let lastTimeout = null;
        return (...args) => {
            if (lastTimeout) {
                clearTimeout(lastTimeout);
            }

            lastTimeout = setTimeout(() => {
                fn(...args);
                clearTimeout(lastTimeout);
            }, duration);
        }
    }
    
    function beginNavbarResizing(evt){
        resizing = true;
        $('#sidebar-separator')
            .get(0)
            .setPointerCapture(evt.pointerId);
    }

    function resizeNavbar(evt){
        if (!resizing) {
            return;
        }

        let newSidebarWidth = evt.clientX;

        if (newSidebarWidth >= 300){
            newSidebarWidth = 300;
        }

        $('#sidebar')
            .css('--sidebar-width', `${newSidebarWidth}px`);
        
        if (newSidebarWidth <= 80) {
            $('#sidebar')
                .css('display', 'none');
        }

        else {
            $('#sidebar')
                .css('display', 'block');
        }
    }

    function endNavbarResizing(evt){
        resizing = false;
        $('#sidebar-separator')
            .get(0)
            .releasePointerCapture(evt.pointerId);
    }

    $('#sidebar-separator')
        .on('pointerdown', beginNavbarResizing)
        .on('pointermove', debouncify(resizeNavbar, 1000 / 60))
        .on('pointerup', endNavbarResizing)
});