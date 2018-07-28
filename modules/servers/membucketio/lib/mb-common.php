<?php
/**
 *  This file is part of Membucket.io Provivisioning Module for WHMCS.
 *
 *  Membucket.io Provivisioning Module for WHMCS is free software: you can
 *  redistribute it and/or modify it under the terms of the Lesser GNU General
 *  Public License as published by the Free Software Foundation, either version
 *  3 of the License, or (at your option) any later version.
 *
 *  Membucket.io Provivisioning Module for WHMCS is distributed in the hope
 *  that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 *  warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with Membucket.io Provivisioning Module for WHMCS.  If not, see
 *  <https://www.gnu.org/licenses/>.
**/
  function CallAPI($ip = "127.0.0.1", $method = "GET", $path = "", $data = false) {
    $curl = curl_init();
    curl_setopt( $curl, CURLOPT_URL, "http://{$ip}:9999/wells{$path}" );

    $data_string = json_encode( $data );
    if ( "GET" != $method ) {
      curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
      curl_setopt( $curl, CURLOPT_HTTPHEADER,
        array(
          "Content-Type: application/json",
          "Content-Length: " . strlen( $data_string )
        )
      );

      if ( $data ) {
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );
      }
    } else {
      curl_setopt( $curl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ) );
      curl_setopt( $curl, CURLOPT_HTTPGET, true );
    }

    curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 3 );
    curl_setopt( $curl, CURLOPT_TIMEOUT,        10 );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
    $result = curl_exec( $curl );
    curl_close( $curl );
    return $result;
  }

  function _Get_User() {
    $user = posix_getpwuid( posix_geteuid() );
    return $user['name'];
  }

  require( 'Well.class.php' );
