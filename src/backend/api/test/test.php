<?php

namespace test {
    class Makes
    {
        public function Makes()
        {
            echo intval(null);
        }
    }

    $make = new Makes();
    $make->Makes();
}
