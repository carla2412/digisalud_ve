// ========================
// Sidebar Toggle
// ========================
document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const closeSidebar = document.getElementById("closeSidebar");

    if (menuToggle) {
        menuToggle.addEventListener("click", () => {
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener("click", () => {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }
});

  // =========================
    // 🔧 DATA (EJEMPLO EDITABLE)
    // =========================
    const MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // Usuarios nuevos (por mes) desglosado
    const usuariosIndMes = [20,34,28,40,32,44,36,30,27,33,38,45];
    const usuariosInstMes = [12,18,16,22,20,24,25,21,19,23,26,28];

    // Organizaciones nuevas por mes
    const organizacionesMes = [2,3,1,4,3,5,2,4,3,2,4,5];

    // Jornadas nuevas por organización (top 6)
    const orgLabels = ['Fundación A','Fundación B','ONG C','Entidad D','Asoc. E','Corp. F'];
    const jornadasPorOrg = [18,14,12,10,9,7];

    // Centros nuevos por organización (top 6)
    const centrosPorOrg = [7,6,5,4,4,3];

    // Nuevos beneficiarios (por mes)
    const beneficiariosMes = [110,140,120,180,150,175,160,155,145,170,185,210];

    // Pesquisas 2025 por mes y total
    const pesquisasMes2025 = [900,1050,980,1200,1100,1300,1250,1190,1150,1380,1420,1500];
    const pesquisasTotales2025 = pesquisasMes2025.reduce((a,b)=>a+b,0);

    // =========================
    // 🧮 KPIs (mes actual simulado: junio = índice 5)
    // =========================
    const idxMesActual = new Date().getMonth(); // 0..11
    const kpiUsuariosMes = usuariosIndMes[idxMesActual] + usuariosInstMes[idxMesActual];
    const kpiOrgMes = organizacionesMes[idxMesActual];
    const kpiJornadasMes =  jornadasPorOrg.slice(0,3).reduce((a,b)=>a+b,0) / 3 | 0; // proxy simple
    const kpiCentrosMes  =  centrosPorOrg.slice(0,3).reduce((a,b)=>a+b,0) / 3 | 0;  // proxy simple
    const kpiBenefMes    = beneficiariosMes[idxMesActual];

    // Pinta KPIs
    document.getElementById('kpiUsuariosNuevos').textContent = kpiUsuariosMes.toLocaleString('es-CO');
    document.getElementById('subUsuariosNuevos').textContent = `Indep.: ${usuariosIndMes[idxMesActual]} | Inst.: ${usuariosInstMes[idxMesActual]}`;
    document.getElementById('kpiOrganizacionesNuevas').textContent = kpiOrgMes.toLocaleString('es-CO');
    document.getElementById('kpiJornadasNuevas').textContent = kpiJornadasMes.toLocaleString('es-CO');
    document.getElementById('kpiCentrosNuevos').textContent = kpiCentrosMes.toLocaleString('es-CO');
    document.getElementById('kpiBeneficiariosNuevos').textContent = kpiBenefMes.toLocaleString('es-CO');
    document.getElementById('kpiPesquisas2025').textContent = pesquisasTotales2025.toLocaleString('es-CO');

    // =========================
    // 🎨 Config común Chart.js
    // =========================
    const grid = { color: 'rgba(0,0,0,0.08)' };
    const ticks = { color: '#6c757d' };
    const legend = { labels: { boxWidth: 12 } };
    const borderRadius = 6;

    // Usuarios nuevos (stacked barras)
    new Chart(document.getElementById('chUsuariosApilado'), {
      type: 'bar',
      data: {
        labels: MESES,
        datasets: [
          { label: 'Independientes', data: usuariosIndMes, borderWidth: 1, borderRadius },
          { label: 'Institucionales', data: usuariosInstMes, borderWidth: 1, borderRadius }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { stacked: true, grid: { display:false }, ticks },
          y: { stacked: true, grid, ticks }
        },
        plugins: { legend }
      }
    });

    // Organizaciones nuevas por mes (línea)
    new Chart(document.getElementById('chOrganizacionesMes'), {
      type: 'line',
      data: {
        labels: MESES,
        datasets: [{
          label: 'Organizaciones',
          data: organizacionesMes,
          tension: .35,
          fill: true
        }]
      },
      options: {
        responsive: true,
        scales: { x:{ grid:{display:false}, ticks }, y:{ grid, ticks } },
        plugins: { legend }
      }
    });

    // Jornadas nuevas por organización (bar horizontal)
    new Chart(document.getElementById('chJornadasPorOrg'), {
      type: 'bar',
      data: {
        labels: orgLabels,
        datasets: [{ label: 'Jornadas', data: jornadasPorOrg, borderWidth:1, borderRadius }]
      },
      options: {
        indexAxis: 'y',
        scales: { x:{ grid, ticks }, y:{ grid:{display:false}, ticks } },
        plugins: { legend }
      }
    });

    // Centros nuevos por organización (bar horizontal)
    new Chart(document.getElementById('chCentrosPorOrg'), {
      type: 'bar',
      data: {
        labels: orgLabels,
        datasets: [{ label: 'Centros', data: centrosPorOrg, borderWidth:1, borderRadius }]
      },
      options: {
        indexAxis: 'y',
        scales: { x:{ grid, ticks }, y:{ grid:{display:false}, ticks } },
        plugins: { legend }
      }
    });

    // Beneficiarios nuevos (línea)
    new Chart(document.getElementById('chBeneficiariosMes'), {
      type: 'line',
      data: {
        labels: MESES,
        datasets: [{ label: 'Beneficiarios', data: beneficiariosMes, tension:.35, fill:true }]
      },
      options: {
        scales: { x:{ grid:{display:false}, ticks }, y:{ grid, ticks } },
        plugins: { legend }
      }
    });

    // Pesquisas 2025 (barras)
    new Chart(document.getElementById('chPesquisas2025'), {
      type: 'bar',
      data: {
        labels: MESES,
        datasets: [{ label: 'Pesquisas', data: pesquisasMes2025, borderWidth:1, borderRadius }]
      },
      options: {
        scales: { x:{ grid:{display:false}, ticks }, y:{ grid, ticks } },
        plugins: { legend }
      }
    });



    // 1️⃣ Pesquisas por tipo de pesquisa
  new Chart(document.getElementById("chPesquisasTipo"), {
    type: "bar",
    data: {
      labels: ["Antropometría", "Sanguíneo", "Medicina General", "Signos Vitales"],
      datasets: [{
        label: "Cantidad de pesquisas",
        data: [120, 80, 150, 90],
        backgroundColor: "rgba(54, 162, 235, 0.6)",
        borderColor: "rgba(54, 162, 235, 1)",
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, title: { display: true, text: "Cantidad" } },
        x: { title: { display: true, text: "Tipo de Pesquisa" } }
      }
    }
  });

  // 2️⃣ Pesquisas por actividad por año
  new Chart(document.getElementById("chPesquisasAnio"), {
    type: "bar",
    data: {
      labels: ["2023", "2024", "2025"],
      datasets: [
        {
          label: "Centros",
          data: [300, 400, 500],
          backgroundColor: "rgba(75, 192, 192, 0.6)"
        },
        {
          label: "Jornadas",
          data: [200, 250, 350],
          backgroundColor: "rgba(255, 159, 64, 0.6)"
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, title: { display: true, text: "Pesquisas" } },
        x: { title: { display: true, text: "Año" } }
      }
    }
  });

  // 3️⃣ Beneficiarios por pesquisa por edad
  new Chart(document.getElementById("chBenefEdad"), {
    type: "bar",
    data: {
      labels: ["<5 años", "5-19 años", ">19 años"],
      datasets: [{
        label: "Beneficiarios",
        data: [50, 180, 220],
        backgroundColor: "rgba(153, 102, 255, 0.6)",
        borderColor: "rgba(153, 102, 255, 1)",
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, title: { display: true, text: "Beneficiarios" } },
        x: { title: { display: true, text: "Grupo de Edad" } }
      }
    }
  });