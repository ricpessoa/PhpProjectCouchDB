<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  class Bootstrap {

    public static function make_input($id, $label, $type, $value = '') {
      echo '<label for="' . $id . '">' . $label . '</label>
					<input class="input-large" id="' . $id . '" name="' . $id . '" type="' . $type . '" value="' . $value . '">';
    }
		
  }

