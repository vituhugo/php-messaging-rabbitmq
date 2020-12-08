<?php


namespace Mensageria;

abstract class Cast {
    public static function ObjArray($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = self::ObjArray($val);
            }
        }
        else $new = $obj;
        return $new;
    }
}