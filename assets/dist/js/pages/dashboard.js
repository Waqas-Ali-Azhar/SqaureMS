$(function () {
  'use strict';

  // Make widgets sortable
  $('.connectedSortable').sortable({
    placeholder: 'sort-highlight',
    connectWith: '.connectedSortable',
    handle: '.card-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex: 999999
  });
  $('.connectedSortable .card-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move');

  // Sortable todo list
  $('.todo-list').sortable({
    placeholder: 'sort-highlight',
    handle: '.handle',
    forcePlaceholderSize: true,
    zIndex: 999999
  });

  // Summernote editor
  $('.textarea').summernote();

  // Date range picker
  $('.daterange').daterangepicker({
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate: moment()
  }, function (start, end) {
    window.alert('You chose: ' + start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  });

  // Knob
  $('.knob').knob();

  // jVectorMap with World Map
  if ($('#world-map').length) {
    $('#world-map').css({ width: '100%', height: '400px' });
    var visitorsData = {
      'US': 398,
      'SA': 400,
      'CA': 1000,
      'DE': 500,
      'FR': 760,
      'CN': 300,
      'AU': 700,
      'BR': 600,
      'IN': 800,
      'GB': 320,
      'RU': 3000
    };
    $('#world-map').vectorMap({
      map: 'world_en',
      backgroundColor: 'transparent',
      regionStyle: {
        initial: {
          fill: 'rgba(255, 255, 255, 0.7)',
          'fill-opacity': 1,
          stroke: 'rgba(0,0,0,.2)',
          'stroke-width': 1,
          'stroke-opacity': 1
        }
      },
      series: {
        regions: [{
          values: visitorsData,
          scale: ['#ffffff', '#0154ad'],
          normalizeFunction: 'polynomial'
        }]
      },
      onRegionLabelShow: function (e, el, code) {
        if (typeof visitorsData[code] !== 'undefined') {
          el.html(el.html() + ': ' + visitorsData[code] + ' new visitors');
        }
      }
    });
  }

  // Sparkline charts (safe)
  const drawSparkline = (id, data) => {
    const el = document.getElementById(id);
    if (el) {
      const s = new Sparkline(el, {
        width: 80,
        height: 50,
        lineColor: '#92c1dc',
        endColor: '#ebf4f9'
      });
      s.draw(data);
    }
  };

  drawSparkline('sparkline-1', [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021]);
  drawSparkline('sparkline-2', [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921]);
  drawSparkline('sparkline-3', [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21]);

  // Calendar
  $('#calendar').datetimepicker({
    format: 'L',
    inline: true
  });

  // Chat widget scrollbar
  $('#chat-box').overlayScrollbars({
    height: '250px'
  });

  // Chart.js - Revenue Chart
  const salesChartCanvas = document.getElementById('revenue-chart-canvas');
  if (salesChartCanvas) {
    const salesChart = new Chart(salesChartCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
          {
            label: 'Digital Goods',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            pointRadius: false,
            data: [28, 48, 40, 19, 86, 27, 90]
          },
          {
            label: 'Electronics',
            backgroundColor: 'rgba(210, 214, 222, 1)',
            borderColor: 'rgba(210, 214, 222, 1)',
            pointRadius: false,
            data: [65, 59, 80, 81, 56, 55, 40]
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        legend: { display: false },
        scales: {
          xAxes: [{ gridLines: { display: false } }],
          yAxes: [{ gridLines: { display: false } }]
        }
      }
    });
  }

  // Doughnut Chart
  const pieChartCanvas = document.getElementById('sales-chart-canvas');
  if (pieChartCanvas) {
    const pieChart = new Chart(pieChartCanvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Instore Sales', 'Download Sales', 'Mail-Order Sales'],
        datasets: [{
          data: [30, 12, 20],
          backgroundColor: ['#f56954', '#00a65a', '#f39c12']
        }]
      },
      options: {
        legend: { display: false },
        maintainAspectRatio: false,
        responsive: true
      }
    });
  }

  // Line Chart (Sales Graph)
  const lineChartCanvas = document.getElementById('line-chart');
  if (lineChartCanvas) {
    const lineChart = new Chart(lineChartCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['2011 Q1', '2011 Q2', '2011 Q3', '2011 Q4', '2012 Q1', '2012 Q2', '2012 Q3', '2012 Q4', '2013 Q1', '2013 Q2'],
        datasets: [{
          label: 'Digital Goods',
          fill: false,
          borderWidth: 2,
          lineTension: 0,
          spanGaps: true,
          borderColor: '#efefef',
          pointRadius: 3,
          pointHoverRadius: 7,
          pointBackgroundColor: '#efefef',
          data: [2666, 2778, 4912, 3767, 6810, 5670, 4820, 15073, 10687, 8432]
        }]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        legend: { display: false },
        scales: {
          xAxes: [{
            ticks: { fontColor: '#efefef' },
            gridLines: { display: false, color: '#efefef', drawBorder: false }
          }],
          yAxes: [{
            ticks: { stepSize: 5000, fontColor: '#efefef' },
            gridLines: { display: true, color: '#efefef', drawBorder: false }
          }]
        }
      }
    });
  }
});
