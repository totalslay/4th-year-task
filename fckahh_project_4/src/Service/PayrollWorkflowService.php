<?php

namespace App\Service;

use App\Entity\PayrollPeriod;
use Symfony\Component\Workflow\WorkflowInterface;

class PayrollWorkflowService
{
    private $workflow;

    public function __construct(WorkflowInterface $payrollPeriodStateMachine)
    {
        $this->workflow = $payrollPeriodStateMachine;
    }

    public function approve(PayrollPeriod $period): bool
    {
        if ($this->workflow->can($period, 'to_approved')){
            $this->workflow->apply($period, 'to_approved'); 
            return true;
        }
        return false;
    }

    public function process(PayrollPeriod $period): bool
    {
        if ($this->workflow->can($period, 'to_processed')){
            $this->workflow->apply($period, 'to_processed'); 
            return true;
        }
        return false;
    }

    public function toDraft(PayrollPeriod $period): bool
    {
        if ($this->workflow->can($period, 'back_to_draft')){
            $this->workflow->apply($period, 'back_to_draft'); 
            return true;
        }
        return false;
    }

    public function getAvailableTransitions(PayrollPeriod $period): array
    {
        return $this->workflow->getEnabledTransitions($period);
    }
}