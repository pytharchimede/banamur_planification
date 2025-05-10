<?php
class Utils
{
    public static function dateEnToutesLettres($date)
    {
        $mois = [
            1 => 'janvier',
            2 => 'février',
            3 => 'mars',
            4 => 'avril',
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet',
            8 => 'août',
            9 => 'septembre',
            10 => 'octobre',
            11 => 'novembre',
            12 => 'décembre'
        ];

        $annee = date('Y', strtotime($date));
        $mois_num = date('n', strtotime($date));
        $jour = date('j', strtotime($date));

        return $jour . ' ' . $mois[$mois_num] . ' ' . $annee;
    }

    // Conversion UTF-8 vers ISO-8859-1 avec mb_convert_encoding (recommandé)
    public static function toMbConvertEncoding($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
    }

    // Conversion UTF-8 vers ISO-8859-1 avec utf8_decode (fallback)
    public static function toUtf8Decode($string)
    {
        return utf8_decode($string);
    }

    public static function montantEnLettre($nombre)
    {
        // Utilise NumberFormatter si disponible (intl)
        if (class_exists('NumberFormatter')) {
            $fmt = new \NumberFormatter('fr_FR', \NumberFormatter::SPELLOUT);
            $lettres = $fmt->format($nombre);
            return ucfirst($lettres) . ' francs CFA';
        }
        // Sinon, version basique
        return $nombre . ' francs CFA';
    }
}
