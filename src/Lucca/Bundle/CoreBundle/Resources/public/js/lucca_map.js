/**
 * @author Alizée Meyer <alizee.m@numeric-wave.eu>
 * @type {{_init: lucca_map._init, _addListeners: lucca_map._addListeners}}
 */

/** Function to create elements for map */
function generateHtmlInfos(p_num, p_url = null, p_infos = null) {
    let html = "";
    if (p_num)
        html += "<b>" + p_num + "</b><br>";
    for (var i = 0; i < p_infos.length; i++) {
        if (p_infos[i] != null && p_infos[i] != "") {
            html += p_infos[i] + "<br>";
        }
    }
    if (p_url && displayResults)
        html += " <a href=\"" + p_url + "\" title=\"> En savoir plus\"> > En savoir plus </a>";
    return html;
}

function createMarker(p_num, p_icon, p_latLng) {
    var tempMarker = new google.maps.Marker({
        position: p_latLng,
        title: p_num,
        icon: {
            url: p_icon,
            scaledSize: new google.maps.Size(25, 30),
        },
        opacity: 1,
    });
    tempMarker.setMap(map);
    return tempMarker;
}

function createInfoWindow(p_id, p_marker, p_htmlInfos, p_htmlInfos2 = null) {
    var infowindow;
    var infowindowClick;
    infowindow = new google.maps.InfoWindow({
        content: "",
    });
    infowindowClick = new google.maps.InfoWindow({
        content: "",
    });
    infowindow.setContent(p_htmlInfos);
    p_marker.addListener('mouseover', function () {
        if (openedIW) {
            openedIW.close();
        }
        infowindowClick.close();
        infowindow.open(map, p_marker);
        setTimeout(function () {
            infowindow.close();
        }, '2000');
        openedIW = infowindow;
    });
    if (p_htmlInfos2) {
        infowindowClick.setContent(p_htmlInfos2);
        p_marker.addListener('click', function () {
            if (openedIW) {
                openedIW.close();
            }
            infowindow.close();
            infowindowClick.open(map, p_marker);
            openedIW = infowindowClick;
        });
    }
    return infowindow;
}

function createFolder(latLng) {
    var request = $.ajax({
        url: Routing.generate('lucca_create_folder'),
        data: {
            lat: latLng.lat,
            lng: latLng.lng,
        },
        success: function (response) {
            let res = JSON.parse(response);
            Swal.fire({
                title: '<strong>Validation de création de dossier localisé</strong>',
                icon: 'info',
                html: res['text'] + '<br>' + res['addr'],
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Non',
            }).then((result) => {
                if (result.value) {
                    window.open(res['url'])
                }
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Erreur dans la recherche de l\'addresse',
                text: response['message'],
            });
        }
    });
}
function cleanMarkers(typeMarker) {
    /** Remove all markers from the map and delete it */
    for (let i = 0; i < datas[typeMarker].length; i++) {
        datas[typeMarker][i].setVisible(false);
        datas[typeMarker][i].setMap(null);
        delete datas[typeMarker][i];
    }
    datas[typeMarker] = [];

    /** Remove cluster for this kind of marker and delete it */
    clusters[typeMarker].repaint();
    clusters[typeMarker].setMap(null);
    delete clusters[typeMarker];
}

function displayMarkers(id) {
    if (document.getElementById(id).style.opacity === "1") {
        let typeMarker = id.replace('filter-','');

        if(datas[typeMarker].length > 0)
            cleanMarkers(typeMarker);
        switch (id) {
            case 'filter-controls':
                createMarkersControls();
                break;
            case 'filter-decisions':
                createMarkersDecisions();
                break;
            case 'filter-folders':
                createMarkersFolder();
                break;
            case 'filter-updates':
                createMarkersUpdating();
                break;
            case 'filter-minutesSpotted':
                createMarkersSpotted();
                break;
            case 'filter-minutesClosedOther':
                createMarkersClosedOther();
                break;
            case 'filter-minutesClosedRegular':
                createMarkersClosedRegular();
                break;
            default:
        }
    }
}

/** Load and display markers when click on the corresponding filter */
$(document).on('click', '.js-filters', function (e) {
    let id = $(this).attr('id');
    let opacity;

    /** First check the state of the button */
    if (document.getElementById(id).style.opacity === "0.3") {
        opacity = 1;
        switch (id) {
            case 'filter-controls':
                createMarkersControls();
                break;
            case 'filter-decisions':
                createMarkersDecisions();
                break;
            case 'filter-folders':
                createMarkersFolder();
                break;
            case 'filter-updates':
                createMarkersUpdating();
                break;
            case 'filter-minutesSpotted':
                createMarkersSpotted();
                break;
            case 'filter-minutesClosedOther':
                createMarkersClosedOther();
                break;
            case 'filter-minutesClosedRegular':
                createMarkersClosedRegular();
                break;
            default:
        }
    } else {
        opacity = 0.3;
        let typeMarker = id.replace('filter-','');
        cleanMarkers(typeMarker);
    }
    document.getElementById(id).style.opacity = opacity;
});


let lucca_map = {

    /**
     * Initiate object
     * @private
     */
    _init: function () {
        let self = this;

        $(document).ready(function () {
        });

        this._addListeners();
    },

    /**
     * Add all js listener
     * @private
     */
    _addListeners: function () {
        let self = this;
        /** set request null */
        var request = null;

        /** Execute ajax request */
        function search(addr) {
            request = $.ajax({
                url: Routing.generate('lucca_find_address'),
                data: {
                    address: addr,
                    displayResults: displayResults,
                },
                success: function (response) {
                    let res = JSON.parse(response);
                    let $attrRempl = $('.js-searchResults');
                    let html = display(res);

                    $attrRempl.html(html);
                    map.setCenter(new google.maps.LatLng(res["geoCode"]["lat"], res["geoCode"]["lon"]));
                    map.setZoom(14);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }

        function generateHtmlResults(p_html, p_json, p_value) {
            if (p_json[p_value]) {
                for (let i = 0; i < p_json[p_value].length; i++) {
                    p_html += "<div class=\"row mt-2 mb-1\">";
                    p_html += "<div class=\"col-1\">";
                    p_html += "<div class=\"pellet pellet-" + p_value + "\">";
                    p_html += "</div></div><div class=\"col-11\">";
                    if (p_json[p_value][i]["num"])
                        p_html += "<b>" + p_json[p_value][i]["num"] + "</b><br>";
                    if (p_json[p_value][i]["address"])
                        p_html += p_json[p_value][i]["address"] + "<br>";
                    if (p_json[p_value][i]["agent"])
                        p_html += p_json[p_value][i]["agent"] + "<br>";
                    if (p_json[p_value][i]["url"])
                        p_html += " <a href=\"" + p_json[p_value][i]["url"] + "\" title=\"> En savoir plus\"> > En savoir plus </a>";
                    p_html += "</div>";
                    p_html += "</div>";
                }
            }

            return p_html;
        }

        /** Format html from json */
        function display(json) {
            let html = "";
            // Add html to show folders open
            html = generateHtmlResults(html, json, "minutesSpotted");
            html = generateHtmlResults(html, json, "foldersOpen");
            html = generateHtmlResults(html, json, "minutesClosedRegular");
            html = generateHtmlResults(html, json, "minutesClosedOther");
            html = generateHtmlResults(html, json, "controls");
            html = generateHtmlResults(html, json, "decisions");
            html = generateHtmlResults(html, json, "folders");
            html = generateHtmlResults(html, json, "updates");

            return html;
        }

        /**
         * Search all folders / minutes / controls in an area
         */
        $(document).on('click', '.js-search', function (e) {
            let addr = $(this).parent().find('input').val();
            search(addr);
        });
        $(document).on('keypress', '#searchAddress', function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                let addr = $(this).parent().find('input').val();
                search(addr);
            }
        });
    }
};

lucca_map._init();