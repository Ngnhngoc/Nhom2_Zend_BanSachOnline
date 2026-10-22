<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    /** @psalm-suppress PossiblyUnusedMethod Called by the Laminas router. */
    public function loginAction(): ViewModel
    {
        $view = new ViewModel();
        // Tắt layout chung (thanh menu và sidebar) để trang đăng nhập hiển thị độc lập
        $view->setTerminal(true);
        return $view;
    }
}
