

<!-- partial  -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="<?= base_url('js/dashboard.js') ?>"></script>
<script src="<?= base_url('js/profesiones.js') ?>"></script>
<!-- FINpartial  -->

<script>
        $(document).ready(function() {
        // Selecciona las alertas con la clase auto-dismiss
        setTimeout(function() {
            $(".auto-dismiss").fadeOut(500, function(){
                $(this).remove(); 
            });
        }, 3000); // 5000ms = 5 segundos
    });

    
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const overlay = document.getElementById('overlay');

    const openSidebar =
        document.getElementById('openSidebar') ||
        document.querySelector('.menu-toggle') ||
        document.querySelector('.navbar-toggler') ||
        document.querySelector('[data-open-sidebar]');

    function abrirSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    }

    function cerrarSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    if (openSidebar) {
        openSidebar.addEventListener('click', function (e) {
            e.preventDefault();
            abrirSidebar();
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', cerrarSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', cerrarSidebar);
    }
});
</script>