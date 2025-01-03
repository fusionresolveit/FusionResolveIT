<?php

declare(strict_types=1);

namespace App\Models;

class ContractItem extends Common
{
  protected $definition = '\App\Models\Definitions\ContractItem';
  protected $titles = ['Contract Item', 'Contract Items'];
  protected $icon = 'box open';
  protected $table = 'contract_item';
  protected $hasEntityField = false;
}
