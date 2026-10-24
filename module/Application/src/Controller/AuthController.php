<?php
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;
use Application\Model\NguoiDungTable;

class AuthController extends AbstractActionController
{
    private $nguoiDungTable;

    public function __construct(NguoiDungTable $nguoiDungTable)
    {
        $this->nguoiDungTable = $nguoiDungTable;
    }

    public function loginAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true); // Tắt layout chung cho trang đăng nhập

        $session = new Container('user_session');

        // Nếu đã đăng nhập, tự động chuyển hướng theo quyền (không cho xem lại form login)
        if (isset($session->user)) {
            if ($session->user['role'] === 'Admin') {
                return $this->redirect()->toRoute('category');
            }
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        
        // Xử lý khi form được submit (phương thức POST)
        if ($request->isPost()) {
            $username = $request->getPost('username');
            $password = $request->getPost('password');

            // Gọi hàm từ NguoiDungTable để lấy thông tin tài khoản
            $user = $this->nguoiDungTable->getByUsername($username);

            // Xác thực: Kiểm tra tài khoản tồn tại và so khớp mật khẩu Bcrypt
            if ($user && password_verify($password, $user['password'])) {
                
                // Đăng nhập thành công -> Lưu Session
                $session->user = [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'role'     => $user['role'],
                    'ho_ten'   => $user['ho_ten'],
                ];

                // Phân quyền: Chuyển hướng theo chức vụ
                if ($user['role'] === 'Admin') {
                    return $this->redirect()->toRoute('category');
                }
                return $this->redirect()->toRoute('home');
            }

            // Báo lỗi ra View nếu sai tài khoản/mật khẩu
            $view->setVariable('error', 'Tài khoản hoặc mật khẩu không chính xác!');
        }

        return $view;
    }

    public function logoutAction()
    {
        // Xóa toàn bộ Session khi đăng xuất
        $session = new Container('user_session');
        $session->getManager()->getStorage()->clear('user_session');
        
        // Trở về trang đăng nhập
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
}