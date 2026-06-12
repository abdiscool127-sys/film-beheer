<?php
/**
 * Helper functies
 */

/**
 * Escape HTML-speciale tekens
 * 
 * Voorkomt XSS-aanvallen door <, >, &, " en ' om te zetten naar HTML-entities.
 * Gebruik dit ALTIJD wanneer je user-input of database-data in HTML zet.
 * 
 * Voorbeeld:
 *   echo e($user_input); // < wordt &lt;
 * 
 * @param mixed $v Waarde om te escapen
 * @return string Geescapte string
 */
function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
