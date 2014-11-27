<?php
/*
Copyright (C) 2014, Siemens AG
Authors: Daniele Fognini, Johannes Najjar

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace Fossology\Lib\BusinessRules;

use Fossology\Lib\Data\ClearingDecision;
use Fossology\Lib\Data\LicenseRef;
use Fossology\Lib\Util\Object;
use Fossology\Lib\Data\DecisionScopes;

/**
 * @brief filter newest license from array of [ClearingDecisions or LicenceRef array]
 * @package Fossology\Lib\BusinessRules
 */
class LicenseFilter extends Object
{

  /** @var ClearingDecisionFilter */
  private $clearingDecisionFilter;

  public function __construct(ClearingDecisionFilter $clearingDecisionFilter)
  {
    $this->clearingDecisionFilter = $clearingDecisionFilter;
  }

  /**
   * @param ClearingDecision[] $editedDecArray
   * @return LicenseRef[]
   */
  public function extractGoodLicensesPerItem($editedDecArray)
  {
    $goodLicenses = $this->selectNewestEditedLicensePerItem($editedDecArray);

    return $goodLicenses;
  }

  /**
   * @param CleringDecision[] $editedLicensesArray
   * @return string[]
   */
  public function extractGoodLicenses($editedLicensesArray)
  {
    $licensesPerId = $this->extractGoodLicensesPerItem($editedLicensesArray);

    $licenses = array();
    foreach ($licensesPerId as $licInfo)
    {
      foreach ($licInfo as $licRef)
      {
        $licenses[] = $licRef->getShortName();
      }
    }
    return $licenses;
  }

  /**
   * @brief $sortedClearingDecArray needs to be sorted with the newest clearingDecision first.
   * @param ClearingDecision[] $sortedClearingDecArray
   * @return LicenseRef[]
   */
  private function selectNewestEditedLicensePerItem($sortedClearingDecArray)
  {
    $clearingDecisions = $this->clearingDecisionFilter->filterCurrentClearingDecisions($sortedClearingDecArray);

    $licenses = array();
    foreach ($clearingDecisions as $itemId => $clearingDecision)
    {
      $currentLicensesByThisItem = array_key_exists($itemId, $licenses) ? $licenses[$itemId] : array();
      $licenses[$itemId] = array_merge($currentLicensesByThisItem, $clearingDecision->getPositiveLicenses());
    }
    return $licenses;
  }

}