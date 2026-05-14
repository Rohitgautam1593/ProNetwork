<?php
class SearchController extends Controller {
    private $searchModel;

    public function __construct() {
        $this->searchModel = $this->model('Search');
    }

    public function index() {
        if(!isLoggedIn()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $term = trim($_GET['q'] ?? '');
        if(empty($term)) {
            echo json_encode(['success' => true, 'results' => []]);
            exit;
        }

        $results = $this->searchModel->globalSearch($term);
        echo json_encode(['success' => true, 'results' => $results]);
    }
}
