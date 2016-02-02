<?php
/**
 * CiviCRM hooks
 */
function superstyler_civicrm_navigationMenu(&$params)
{
    //  Get the maximum key of $params.
    $nextKey = (max(array_keys($params)));
    // Check for Administer navID.
    $AdministerKey = '';
    foreach ($params as $k => $v) {
        if ($v['attributes']['name'] == 'Administer') {
            $AdministerKey = $k;
        }
    }
    // Check for Parent navID.
    foreach ($params[$AdministerKey]['child'] as $k => $v) {
        if ($v['attributes']['name'] == 'CTRL') {
            $parentKey = $v['attributes']['navID'];
        }
    }
    // If Parent navID doesn't exist create.
    if (!isset($parentKey)) {
        // Create parent array
        $parent = array(
            'attributes' => array(
                'label' => 'CTRL',
                'name' => 'CTRL',
                'url' => null,
                'permission' => 'access CiviCRM',
                'operator' => null,
                'separator' => 0,
                'parentID' => $AdministerKey,
                'navID' => $nextKey,
                'active' => 1),
            'child' => null);
        // Add parent to Administer
        $params[$AdministerKey]['child'][$nextKey] = $parent;
        $parentKey = $nextKey;
        $nextKey++;
    }
    // Create child(s) array
    $child = array(
        'attributes' => array(
            'label' => 'SuperStyler',
            'name' => 'ctrl_superstyler',
            'url' => 'civicrm/ctrl/superstyler',
            'permission' => 'access CiviCRM',
            'operator' => null,
            'separator' => 0,
            'parentID' => $parentKey,
            'navID' => $nextKey,
            'active' => 1),
        'child' => null);
    // Add child(s) for this extension
    $params[$AdministerKey]['child'][$parentKey]['child'][$nextKey] = $child;
}
?>