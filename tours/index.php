<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Map">
        <meta name="author" content="Daniel Brown">
        <title>Map</title>
        <style>
            .map {
                width: 100%;
                height: 400px;
            }
            .ol-popup {
                position: absolute;
                background-color: white;
                box-shadow: 0 1px 4px rgba(0,0,0,0.2);
                padding: 15px;
                border-radius: 10px;
                border: 1px solid #cccccc;
                bottom: 12px;
                left: -50px;
                min-width: 280px;
            }
            .ol-popup:after, .ol-popup:before {
                top: 100%;
                border: solid transparent;
                content: " ";
                height: 0;
                width: 0;
                position: absolute;
                pointer-events: none;
            }
            .ol-popup:after {
                border-top-color: white;
                border-width: 10px;
                left: 48px;
                margin-left: -10px;
            }
            .ol-popup:before {
                border-top-color: #cccccc;
                border-width: 11px;
                left: 48px;
                margin-left: -11px;
            }
            .ol-popup-closer {
                text-decoration: none;
                position: absolute;
                top: 2px;
                right: 8px;
            }
            .ol-popup-closer:after {
                content: "✖";
            }
        </style>
    </head>
    <body>
        <script src='https://cdn.jsdelivr.net/npm/ol@v7.1.0/dist/ol.js'></script>
        <script src='https://code.jquery.com/jquery-3.6.2.min.js' integrity='sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=' crossorigin='anonymous'></script>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/ol@v7.1.0/ol.css'>
        <div id='content-placeholder'></div>
        <div id='map' style='width:100%; height:30em;'></div>
        <!-- position: fixed; top: 0; left: 0; bottom: 0; right: 0; -->
        <div id="popup" class="ol-popup">
            <a href="#" id="popup-closer" class="ol-popup-closer"></a>
            <div id="popup-content"></div>
        </div>

        <template>
            <div class='alert alert-light alert-dismissible fade show' role='alert'>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                <span class='title'>
                    <slot name='info'></slot>
                </span>
                <span id='typeBadge' class='badge badge-secondary'>
                    <slot name='type'></slot>
                </span><br />
                <slot name='data' id='data'></slot> 
                <a href='' target='_blank'>More Details</a>
            </div>
        </template>
    </body>
<script>

let featureLayer;
let vectorLines;
let vectorLineLayer;
let map;
let overlay;

function addPoint(location, details){
    let lat = location.lat;
    let lon = location.lon;

    var locationPointFeature = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat])),
        name: location.name + ' (' + location.description + ')'
    });

    locationPointFeature.setId(location.id);

    var icon = 'images/dot-point.png' 
    colour = details.style.colour;

    if(location.type){
        if(location.type === 'point'){
            icon = 'images/dot-point.png';
        }
        if(location.type === 'intersection'){
            icon = 'images/dot-intersection.png';
        }
        if(location.type === 'route-intersection'){
            icon = 'images/dot-route-intersection.png';
            colour = '#ffffff';
        }
    }
    locationPointFeature.setStyle(new ol.style.Style({
        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */({
            color: colour,
            crossOrigin: 'anonymous',
            src: icon
        })),
    }));

    featureLayer.getSource().addFeature(locationPointFeature);
};

function populateRidesViaApi(){
    jQuery.when(
        jQuery.ajax({
            url: "data/",
        })
    )
    .done(function(rides){
        for(const rideName in rides){
            const ride = rides[rideName];

            if(ride.routes){
                ride.routes.forEach(function(route){
                    addConnection(route, ride.details);
                });
            }

            if(ride.locations){
                ride.locations.forEach(function(location) {
                    addPoint(location, ride.details);
                });
            }
        }
    });
}

function addConnection(connection, details){
    var points = [
        [connection.from.lon, connection.from.lat],
        [connection.to.lon, connection.to.lat]];

    for (var i = 0; i < points.length; i++) {
        points[i] = ol.proj.transform(points[i], 'EPSG:4326', 'EPSG:3857');
    }

    var featureLine = new ol.Feature({
        geometry: new ol.geom.LineString(points),
        type: details.type,
        name: details.name,
        from: connection.from.name,
        to: connection.to.name,
        description: connection.description   
    });

    var dash;
    if(details.style.line){
        dash = details.style.line.toLowerCase() === "solid" ? [1,0]: [10, 10];
    }
    else {
        dash = [1,0];
    }
    featureLine.setStyle(new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: details.style.colour,
            width: 3,
            lineDash: dash
        })
    }));

    vectorLine.addFeature(featureLine);
    vectorLineLayer.setSource(vectorLine);
}


function createMap(){

    featureLayer = new ol.layer.Vector({
        style: function (feature) {
            return feature.get('style');
        },
        source: new ol.source.Vector(),
        visible: true
    });

    vectorLine = new ol.source.Vector({});
    
    vectorLineLayer = new ol.layer.Vector({'id':'lines'});

    const container = document.getElementById('popup');
    const content = document.getElementById('popup-content');
    const closer = document.getElementById('popup-closer');

    closer.onclick = function () {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };

    overlay = new ol.Overlay({
        element: container,
        autoPan: {
            animation: {
            duration: 250,
            },
        },
    });

    map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM(),
            }),
            featureLayer,
            vectorLineLayer
        ],
        overlays: [overlay],
        view: new ol.View({
            center: ol.proj.fromLonLat([-2.89479, 54.093409]),
            zoom: 5,
        }),
        controls: ol.control.defaults.defaults().extend([
            new ol.control.FullScreen()
        ]),
    });
}

function registerMapHandler(){
    map.on('click', function (evt) {

        var feature = map.forEachFeatureAtPixel(evt.pixel,
            function (feature) {
                return feature;
            });

        if (feature) {

            let name = feature.get('name');
            let type = feature.get('type');   
            let from = feature.get('from');   
            let to = feature.get('to');   
            let description = feature.get('description');

            descriptionStr = '';
            if(name){
                descriptionStr = descriptionStr + name;
            }
            if(type){
                descriptionStr = descriptionStr + ". " + type;
            }
            if(from){
                descriptionStr = descriptionStr + ". " + from;
            }
            if(to){
                descriptionStr = descriptionStr + " to " + to;
            }
            if(description){
                descriptionStr = descriptionStr + ". " + description;
            }


            const coordinate = evt.coordinate;
            const content = document.getElementById('popup-content');

            content.innerHTML = '<p>'+ descriptionStr + '</p>';
            overlay.setPosition(coordinate);
        }
    });
}

createMap();
populateRidesViaApi();
registerMapHandler();

</script>
</html>