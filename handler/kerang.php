<?php

use \LINE\LINEBot\MessageBuilder\TextMessageBuilder as TextMessageBuilder;

function kerang($query, $userId){
    if ($query == null){
        $result = new TextMessageBuilder("Kerang Ajaib Memberi Anda Sebuah Jawaban!\n\nCara Pakai:\n/kerang [apakah] [pertanyaan]\n\nExample:\n/kerang apakah aku lucu?");
    }
        if($query == "apakah"){
            $list_jwb = array(
                'Ya',
                'Tidak',
                'Bisa jadi',
                'Mungkin',
                'Tentu tidak',
                'Coba tanya lagi'
                );
            $jaws = array_rand($list_jwb);
            $jawab = $list_jwb[$jaws];
            $result = new TextMessageBuilder($jawab);
        }
            else {
                $result = new TextMessageBuilder("Kata kunci salah.");
            }
return($result);
}