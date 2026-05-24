<?php
class PagesController extends Controller {
    public function accessibility() {
        $data = [
            'title' => 'Accessibility Statement',
            'current_page' => 'accessibility'
        ];
        $this->view('pages/accessibility', $data);
    }

    public function user_agreement() {
        $data = [
            'title' => 'User Agreement',
            'current_page' => 'user_agreement'
        ];
        $this->view('pages/user_agreement', $data);
    }

    public function privacy_policy() {
        $data = [
            'title' => 'Privacy Policy',
            'current_page' => 'privacy_policy'
        ];
        $this->view('pages/privacy_policy', $data);
    }

    public function cookie_policy() {
        $data = [
            'title' => 'Cookie Policy',
            'current_page' => 'cookie_policy'
        ];
        $this->view('pages/cookie_policy', $data);
    }

    public function brand_policy() {
        $data = [
            'title' => 'Brand Policy',
            'current_page' => 'brand_policy'
        ];
        $this->view('pages/brand_policy', $data);
    }

    public function guest_controls() {
        $data = [
            'title' => 'Guest Controls',
            'current_page' => 'guest_controls'
        ];
        $this->view('pages/guest_controls', $data);
    }

    public function community_guidelines() {
        $data = [
            'title' => 'Community Guidelines',
            'current_page' => 'community_guidelines'
        ];
        $this->view('pages/community_guidelines', $data);
    }
}
