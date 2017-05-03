<?php

namespace WhichBrowser\Analyser;

use WhichBrowser\Constants;
use WhichBrowser\Model\Version;

trait Corrections {

	private function &applyCorrections() {
		if ( isset( $this->data->browser->name ) && isset( $this->data->browser->using ) ) {
			$this->hideBrowserBasedOnUsing();
		}

		if ( isset( $this->data->browser->name ) && isset( $this->data->os->name ) ) {
			$this->hideBrowserBasedOnOperatingSystem();
		}

		if ( isset( $this->data->browser->name ) && Constants\DeviceType::TELEVISION == $this->data->device->type ) {
			$this->hideBrowserOnDeviceTypeTelevision();
		}

		if ( isset( $this->data->browser->name ) && Constants\DeviceType::GAMING == $this->data->device->type ) {
			$this->hideBrowserOnDeviceTypeGaming();
		}

		if ( Constants\DeviceType::TELEVISION == $this->data->device->type ) {
			$this->hideOsOnDeviceTypeTelevision();
		}

		if ( isset( $this->data->browser->name ) && isset( $this->data->engine->name ) ) {
			$this->fixMidoriEngineName();
		}

		if ( isset( $this->data->browser->name ) && isset( $this->data->engine->name ) ) {
			$this->fixNineSkyEngineName();
		}

		if ( isset( $this->data->browser->name ) && isset( $this->data->browser->family ) ) {
			$this->hideFamilyIfEqualToBrowser();
		}

		return $this;
	}


	private function hideFamilyIfEqualToBrowser() {
		if ( $this->data->browser->name == $this->data->browser->family->name ) {
			unset( $this->data->browser->family );
		}
	}

	private function fixMidoriEngineName() {
		if ( 'Midori' == $this->data->browser->name && 'Webkit' != $this->data->engine->name ) {
			$this->data->engine->name = 'Webkit';
			$this->data->engine->version = null;
		}
	}

	private function fixNineSkyEngineName() {
		if ( 'NineSky' == $this->data->browser->name && 'Webkit' != $this->data->engine->name ) {
			$this->data->engine->name = 'Webkit';
			$this->data->engine->version = null;
		}
	}

	private function hideBrowserBasedOnUsing() {
		if ( 'Chrome' == $this->data->browser->name ) {
			if ( $this->data->browser->isUsing( 'Electron' ) || $this->data->browser->isUsing( 'Qt' ) ) {
				unset( $this->data->browser->name );
				unset( $this->data->browser->version );
			}
		}
	}

	private function hideBrowserBasedOnOperatingSystem() {
		if ( 'Series60' == $this->data->os->name && 'Internet Explorer' == $this->data->browser->name ) {
			$this->data->browser->reset();
			$this->data->engine->reset();
		}

		if ( 'Series80' == $this->data->os->name && 'Internet Explorer' == $this->data->browser->name ) {
			$this->data->browser->reset();
			$this->data->engine->reset();
		}

		if ( 'Lindows' == $this->data->os->name && 'Internet Explorer' == $this->data->browser->name ) {
			$this->data->browser->reset();
			$this->data->engine->reset();
		}

		if ( 'Tizen' == $this->data->os->name && 'Chrome' == $this->data->browser->name ) {
			$this->data->browser->reset([
				'family' => isset( $this->data->browser->family ) ? $this->data->browser->family : null,
			]);
		}

		if ( 'Ubuntu Touch' == $this->data->os->name && 'Chromium' == $this->data->browser->name ) {
			$this->data->browser->reset([
				'family' => isset( $this->data->browser->family ) ? $this->data->browser->family : null,
			]);
		}
	}

	private function hideBrowserOnDeviceTypeGaming() {
		if ( isset( $this->data->device->model ) && 'Playstation 2' == $this->data->device->model && 'Internet Explorer' == $this->data->browser->name ) {
			$this->data->browser->reset();
		}
	}

	private function hideBrowserOnDeviceTypeTelevision() {
		switch ( $this->data->browser->name ) {
			case 'Firefox':
				if ( ! $this->data->isOs( 'Firefox OS' ) ) {
					unset( $this->data->browser->name );
					unset( $this->data->browser->version );
				}
				break;

			case 'Internet Explorer':
				$valid = false;

				if ( isset( $this->data->device->model ) && in_array( $this->data->device->model, [ 'WebTV' ], true ) ) {
					$valid = true;
				}

				if ( ! $valid ) {
					unset( $this->data->browser->name );
					unset( $this->data->browser->version );
				}

				break;

			case 'Chrome':
			case 'Chromium':
				$valid = false;

				if ( isset( $this->data->os->name ) && in_array( $this->data->os->name, [ 'Google TV', 'Android' ], true ) ) {
					$valid = true;
				}
				if ( isset( $this->data->device->model ) && in_array( $this->data->device->model, [ 'Chromecast' ], true ) ) {
					$valid = true;
				}

				if ( ! $valid ) {
					unset( $this->data->browser->name );
					unset( $this->data->browser->version );
				}

				break;
		}
	}

	private function hideOsOnDeviceTypeTelevision() {
		if ( isset( $this->data->os->name ) && ! in_array( $this->data->os->name, [ 'Aliyun OS', 'Tizen', 'Android', 'Android TV', 'FireOS', 'Google TV', 'Firefox OS', 'OpenTV', 'webOS' ], true ) ) {
			$this->data->os->reset();
		}
	}
}
