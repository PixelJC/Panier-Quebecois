<?php

require_once PQ_VENDOR_DIR . '/fpdf184/fpdf.php';

class PQ_FPDF extends FPDF {

    public $col = 0;
    public $y0;
    public $col_width;
    public $margin;

    function SetCol($col) {
        // Set position at a given column
        $this->col = $col;
        $x = $this->margin + $col * $this->col_width;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    function AcceptPageBreak() {
        // Method accepting or not automatic page break
        if($this->col<2)
        {
            // Go to next column
            $this->SetCol($this->col+1);
            // Set ordinate to top
            $this->SetY($this->y0);
            // Keep on page
            return false;
        }
        else
        {
            // Go back to first column
            $this->SetCol(0);
            // Page break
            return true;
        }
    }
}