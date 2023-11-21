/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
jQuery(function($) {


    /**
     * For all Media Type
     */
    const all_media_type = document.getElementById('all-media-type');

    new Chart(all_media_type, {
        type: 'bar',
        data: {
            labels: view_analytics_media_view.all_media_type.media_label,
            datasets: [{
                label: view_analytics_media_view.all_media_type.label,
                data: view_analytics_media_view.all_media_type.count,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
            }],
            hoverOffset: 4,
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                onClick: function (e) {
                    debugger;
                    var activePointLabel = this.getElementsAtEvent(e)[0]._model.label;
                    alert(activePointLabel);
                }
            }
        }
    });



      /**
     * For all Media View Type
     */
      const all_media_view_type = document.getElementById('all-media-view-type');

      new Chart(all_media_view_type, {
          type: 'bar',
          data: {
              labels: view_analytics_media_view.all_media_view_type.media_label,
              datasets: [{
                  label: view_analytics_media_view.all_media_view_type.label,
                  data: view_analytics_media_view.all_media_view_type.count,
                  backgroundColor: [
                      'rgb(255, 99, 132)',
                      'rgb(54, 162, 235)',
                      'rgb(255, 205, 86)'
                  ],
              }],
              hoverOffset: 4
          }
      });
});
/******/ })()
;