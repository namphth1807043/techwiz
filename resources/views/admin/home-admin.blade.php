@extends('layout.admin-master')

@section('content')

    <style>
        body {
            padding: 0;
            margin: 0;
        }

        #map {
            height: 92vh;
            width: 100%;
        }

        .floating-panel {
            position: absolute;
            top: 20px;
            left: 5%;
            z-index: 5;
            background-color: #fff;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto', 'sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }

        .floating-panel {
            margin-left: -52px;
        }
    </style>
    <script src="https://www.gstatic.com/firebasejs/5.9.1/firebase.js"></script>
    <div id="map" style="height: 80vh">
    </div>
    <script>

        var config = {
            apiKey: "AIzaSyB6EvN5u7zMqsylmoqh2lX_EsFMrV1cqm8",
            authDomain: "hello-firebase-2019001.firebaseapp.com",
            databaseURL: "https://hello-firebase-2019001.firebaseio.com",
            projectId: "hello-firebase-2019001",
            storageBucket: "hello-firebase-2019001.appspot.com",
            messagingSenderId: "463492007629"
        };

        firebase.initializeApp(config);
        var db = firebase.firestore();

        var markerMap = {};

        function deleteMarker(id) {
            db.collection("complaints").doc(id).delete().then(function () {

            }).catch(function (error) {

            });

            db.collection("helps").doc(id).delete().then(function () {

            }).catch(function (error) {

            });

            markerMap[id].setMap(null)

        }

        var map;

        function initMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(initialLocation);
                });
            }
            // Map options
            var styleArray = [
                {
                    "featureType": "poi",
                    "elementType": "labels.text",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi.business",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                }
            ];

            var options = {
                zoom: 15,
                minZoom: 13,
                styles: styleArray,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_CENTER
                },
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                scaleControl: true,
                streetViewControl: true,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                fullscreenControl: true
            };
            map = new google.maps.Map(document.getElementById('map'), options);

            var images = {
                jam:
                    {
                        url: 'https://www.tycoifs.ca/wps/wcm/connect/61557331-2b17-4237-a6fe-e77ecdf8dbbb/pulse12.gif?MOD=AJPERES&CACHEID=ROOTWORKSPACE-61557331-2b17-4237-a6fe-e77ecdf8dbbb-kNcW.Bt',
                        scaledSize: new google.maps.Size(70, 70),
                        origin: new google.maps.Point(0, 0),
                        // anchor: new google.maps.Point(0, 32)
                    },
                accident:
                    {
                        url: 'https://hotelmarkovo.bg/en/wp-content/uploads/2018/05/hotelmarkovogreenpulse.gif',
                        scaledSize: new google.maps.Size(70, 70),
                        origin: new google.maps.Point(0, 0),
                        // anchor: new google.maps.Point(0, 32)
                    },
                disaster:
                    {
                        url: 'https://iwant2study.org/lookangejss/01_measurement/ejss_model_horizontalverticalquiz01/horizontalvertical/giphy.gif',
                        scaledSize: new google.maps.Size(70, 70),
                        origin: new google.maps.Point(0, 0),
                        // anchor: new google.maps.Point(0, 32)
                    }
            };

            // Add Marker Function
            function addMarker(doc, id, help) {

                var marker = new google.maps.Marker({
                    position: {lat: parseFloat(doc.latitude), lng: parseFloat(doc.longitude)},
                    map: map,
                    help: help
                });

                markerMap[id] = marker;

                // Check for customicon

                if (doc.type === "jam") {
                    marker.setIcon(images.jam);
                } else if (doc.type === "accident") {
                    marker.setIcon(images.accident)
                } else if (doc.type === "disaster") {
                    marker.setIcon(images.disaster)
                } else if (doc.type === undefined) {
                    marker.setIcon({
                        url: 'http://www.jamiekatzpetdetective.com/stick_figure_help_button_500_clr_9911.gif',
                        scaledSize: new google.maps.Size(50, 50),
                        // origin: new google.maps.Point(0, 0),
                        // anchor: new google.maps.Point(0, 32)
                    })
                }

                var contentString =
                    '<div id="content">' +
                    // '<h3>' + doc.type + '</h3>' +
                    // '<h6>' + doc.title + '</h6>' +
                    '<div>' +
                    '<button onclick=" deleteMarker(\'' + id + '\')">Remove</button>' +
                    '</div>' +
                    '<div>' +
                    '<span>' + moment(doc.time.toDate()).fromNow() + '</span>' +
                    '</div>' +
                    '</div>';

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                marker.addListener('click', function () {
                    infowindow.open(map, marker);
                });


            }

            db.collection("complaints").onSnapshot(function (querySnapshot) {
                querySnapshot.forEach(function (doc) {
                    addMarker(doc.data(), doc.id, false)
                });
            }, function (error) {
                console.log("loi" + error)
            });

            db.collection("helps").onSnapshot(function (querySnapshot) {
                querySnapshot.forEach(function (doc) {
                    addMarker(doc.data(), doc.id, true)
                });
            }, function (error) {
                console.log("loi" + error)
            });

        }

        // });

    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZkhD8Q5_XkZEthioPUXM0_bYX3Lp56WI&callback=initMap">
    </script>
@endsection
