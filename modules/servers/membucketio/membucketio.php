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
  if ( ! defined( "WHMCS" ) )
      die( "This file cannot be accessed directly" );

  require( "lib/mb-common.php" );

  function membucketio_MetaData()
  {
    return array(
      'APIVersion' => '1.1',
      'DisplayName' => 'Membucket.io for cPanel',
      'RequiresServer' => true,
    );
  }

  function membucketio_ConfigOptions()
  {
    return array(
      'Buckets' => array(
        'Default' => '1',
        'Description' => '',
        'Size' => '3',
        'Type' => 'text',
      ),
    );
  }

  function _updateQuotas(array $params, $amount) {
    $data = [
  //    'key'     => $params['serverpassword'],
  //    'keyUser' => $params['serverusername'],
      'user'    => $params['username'],
      'value'   => $amount,
    ];
    return CallAPI( $params['serverip'], "POST", "/quota" .
      '?key=' . $params['serverpassword'] .
      '&keyUser=' . $params['serverusername'], $data );
  }

  /**
   * Provision a new instance of a product/service.
   *
   * Attempt to provision a new instance of a given product/service. This is
   * called any time provisioning is requested inside of WHMCS. Depending upon the
   * configuration, this can be any of:
   * * When a new order is placed
   * * When an invoice for a new order is paid
   * * Upon manual request by an admin user
   *
   * @param array $params common module parameters
   *
   * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
   *
   * @return string "success" or an error message
   */
  function membucketio_CreateAccount(array $params)
  {
    try {
      $response = _updateQuotas( $params, intval( $params['Buckets'] ) );
      if ( "true" !== $response && true !== $response )
        throw new Exception( $response );
    } catch (Exception $e) {
      // Record the error in WHMCS's module log.
      logModuleCall(
        'membucketio',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
      );

      return $e->getMessage();
    }

    return 'success';
  }

  /**
   * Suspend an instance of a product/service.
   *
   * Called when a suspension is requested. This is invoked automatically by WHMCS
   * when a product becomes overdue on payment or can be called manually by admin
   * user.
   *
   * @param array $params common module parameters
   *
   * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
   *
   * @return string "success" or an error message
   */
  function membucketio_SuspendAccount(array $params)
  {
    try {
      $response = _updateQuotas( $params, - intval( $params['Buckets'] ) );
      if ( "true" !== $response && true !== $response )
        throw new Exception( $response );
    } catch (Exception $e) {
      // Record the error in WHMCS's module log.
      logModuleCall(
        'membucketio',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
      );

      return $e->getMessage();
    }

    return 'success';
  }

  /**
   * Un-suspend instance of a product/service.
   *
   * Called when an un-suspension is requested. This is invoked
   * automatically upon payment of an overdue invoice for a product, or
   * can be called manually by admin user.
   *
   * @param array $params common module parameters
   *
   * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
   *
   * @return string "success" or an error message
   */
  function membucketio_UnsuspendAccount(array $params)
  {
    try {
      $response = _updateQuotas( $params, intval( $params['Buckets'] ) );
      if ( "true" !== $response && true !== $response )
        throw new Exception( $response );
    } catch (Exception $e) {
      // Record the error in WHMCS's module log.
      logModuleCall(
        'membucketio',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
      );

      return $e->getMessage();
    }

    return 'success';
  }

  /**
   * Terminate instance of a product/service.
   *
   * Called when a termination is requested. This can be invoked automatically for
   * overdue products if enabled, or requested manually by an admin user.
   *
   * @param array $params common module parameters
   *
   * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
   *
   * @return string "success" or an error message
   */
  function membucketio_TerminateAccount(array $params)
  {
    try {
      $response = _updateQuotas( $params, - intval( $params['Buckets'] ) );
      if ( "true" !== $response && true !== $response )
        throw new Exception( $response );
    } catch (Exception $e) {
      // Record the error in WHMCS's module log.
      logModuleCall(
        'membucketio',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
      );

      return $e->getMessage();
    }

    return 'success';
  }

  /**
   * Test connection with the given server parameters.
   *
   * Allows an admin user to verify that an API connection can be
   * successfully made with the given configuration parameters for a
   * server.
   *
   * When defined in a module, a Test Connection button will appear
   * alongside the Server Type dropdown when adding or editing an
   * existing server.
   *
   * @param array $params common module parameters
   *
   * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
   *
   * @return array
   */
  function membucketio_TestConnection(array $params)
  {
    try {
      if ( "root" != $params['serverusername'] )
        throw new Exception( "Membucket currently only allows root to make remote changes" );
      if ( "" == $params['serverpassword'] )
        throw new Exception( "Membucket requires an access key in the password field" );
      if ( "" == $params['serverip'] )
        throw new Exception( "Membucket requires a server IP" );

      $response = CallAPI( $params['serverip'], "GET",
                            "?key=" . $params['serverpassword'] . 
                            "&keyUser=" . $params['serverusername'] );
      if ( "" == $response )
        throw new Exception( "Could not connect to Membucket at {$params['serverip']}:9999 ." );

      foreach ( json_decode( $response, true ) as $w )
        if ( "Bad Arguments" == $w )
          throw new Exception( "Not Authorized (wrong user or key)" );
      
      $success = true;
      $errorMsg = '';
    } catch (Exception $e) {
      // Record the error in WHMCS's module log.
      logModuleCall(
        'membucketio',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
      );

      $success = false;
      $errorMsg = $e->getMessage();
    }

    return array(
      'success' => $success,
      'error' => $errorMsg,
    );
  }
