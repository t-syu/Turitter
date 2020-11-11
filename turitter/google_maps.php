<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) { //直接入られた時の防止
  $_SESSION['time'] = time();

  $members =$pdo -> prepare('SELECT * FROM members WHERE id=?');
  $members -> execute(array($_SESSION['id']));
  $member = $members -> fetch();
}else {
  header('location: login.php');
  exit();
}
?>
<!DOCTYPE html >
<html lang=ja>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <title>Using MySQL and PHP with Google Maps</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    #map {
      height: 100%;
    }
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
  </style>
</head>

<body>
  <div id="post">
    <h1>魚の釣り状況確認MAP</h1>
    <div style="text-align: right"><a href="index.php">掲示板に戻る</a></div>
  </div>
  <div id="map"></div>

  <script>
    var customLabel = {
      '1': {
        label: '1月'
      },
      '2': {
        label: '2月'
      },
      '3': {
        label: '3月'
      },
      '4': {
        label: '4月'
      },
      '5': {
        label: '5月'
      },
      '6': {
        label: '6月'
      },
      '7': {
        label: '7月'
      },
      '8': {
        label: '8月'
      },
      '9': {
        label: '9月'
      },
      '10': {
        label: '10月'
      },
      '11': {
        label: '11月'
      },
      '12': {
        label: '12月'
      }
    };

    function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
      center: new google.maps.LatLng(35.695235, 139.736927),
      zoom: 7,
      gestureHandling: 'cooperative'
    });
    // var infoWindow = new google.maps.InfoWindow;
    function attachMessage(marker, msg) {
      google.maps.event.addListener(marker, 'click', function(event) {
        new google.maps.InfoWindow({
          content: msg
        }).open(marker.getMap(), marker);
        });
    }

      // xmlを作るファイル名を記入する。xmlファイルも良いがデータは固定化される。
      downloadUrl('outputxml.php', function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName('marker');

        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");
          var message = markers[i].getAttribute("message");
          var post_picture = markers[i].getAttribute("post_picture");
          var created = markers[i].getAttribute("datetime");
          var monthstamp = markers[i].getAttribute("monthstamp");

          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));

          var html = "<b>" + name + "</b> <br/>" + message  + "<br/>" + datetime + ' 撮影日' + '<img src="member_post_picture/' + post_picture + '" width="100% height="100%" alt=' + post_picture + '>';

          var icon = customLabel[monthstamp] || {};

          var marker = new google.maps.Marker({
            map: map,
            position: point,
            label: icon.label
          });
          attachMessage(marker, html)
        }
      });
    }


    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    };

    function doNothing() {};
  </script>
  <script defer 
  src="https://maps.googleapis.com/maps/api/js?key=[yourkey]&callback=initMap">
  </script>
</body>
</html>