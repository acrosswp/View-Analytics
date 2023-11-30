/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
jQuery(function($) {

    /**
     * For all Media View Type
     */
    const all_media_view_type = document.getElementById('all-media-view-type');

    new Chart(all_media_view_type, {
        data: {
            labels: view_analytics_media_view.all_media_type.media_label,
            datasets: [{
                    type: 'bar',
                    label: view_analytics_media_view.all_media_type.label,
                    data: view_analytics_media_view.all_media_type.count,
                },
                {
                    type: 'bar',
                    label: view_analytics_media_view.all_media_view_type.label,
                    data: view_analytics_media_view.all_media_view_type.count,
            }],
            hoverOffset: 4
        }
    });
});
/******/ })()
;