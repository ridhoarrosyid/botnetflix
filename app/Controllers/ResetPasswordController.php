<?php

namespace App\Controllers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

class ResetPasswordController extends BaseController
{
    public function reset_password()
    {

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['message' => 'Method not allowed'])->setStatusCode(405);
        }
        $email = $this->request->getVar('email');
        $current_password = $this->request->getVar('current_password');
        $new_password = $this->request->getVar('new_password');
        $profile_name = $this->request->getVar("profile");
        $profile_pin = $this->request->getVar('pin');

        if (!$email || !$current_password || !$new_password) {
            return $this->response->setJSON(['message' => 'Invalid parameters',])->setStatusCode(400);
        }

        $host = 'http://localhost:4444/'; // URL Selenium server
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

        try {
            $driver->get('https://www.netflix.com/id/login');
            $driver->findElement(WebDriverBy::id(':rc:'))->sendKeys($email);
            $driver->findElement(WebDriverBy::id(':rf:'))->sendKeys($current_password);
            $driver->findElements(WebDriverBy::className('e1ax5wel2'))[0]->click();

            $driver->get('https://www.netflix.com/browse');



            $driver->findElement(WebDriverBy::cssSelector('.profile-link'))->click();
            $inputNumbers = $driver->findElements(WebDriverBy::className('pin-number-input'));
            foreach ($inputNumbers as $int => $inputNumber) {
                $inputNumber->sendKeys($profile_pin[$int]);
            }


            $driver->get('https://www.netflix.com/password');
            $driver->findElement(WebDriverBy::id('id_currentPassword'))->sendKeys($current_password);
            $driver->findElement(WebDriverBy::id('id_newPassword'))->sendKeys($new_password);
            $driver->findElement(WebDriverBy::id('id_confirmNewPassword'))->sendKeys($new_password);
            $driver->findElement(WebDriverBy::id('btn-save'))->click();

            sleep(20);
            $success = $driver->findElement(WebDriverBy::className('default-ltr-cache-mkkf9p'))->getText();
            if (strpos($success, 'Sandimu sudah diubah') !== false) {
                $response = ['message' => 'Password successfully changed'];
            } else {
                $response = ['message' => 'Failed to change password'];
            }
        } catch (\Exception $e) {
            $response = ['message' => 'An error occurred: ' . $e->getMessage()];
        } finally {
            $driver->quit();
        }

        return $this->response->setJSON($response)->setStatusCode(200);
    }
}
