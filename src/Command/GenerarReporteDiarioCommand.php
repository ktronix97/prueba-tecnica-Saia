<?php
// src/Command/GenerarReporteDiarioCommand.php
namespace App\Command;

use App\Service\ReporteService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reporte:diario',
    description: 'Genera y persiste los reportes diarios de tareas.'
)]
class GenerarReporteDiarioCommand extends Command
{
    use LockableTrait;

    public function __construct(private ReporteService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::OPTIONAL, 'Fecha a procesar (YYYY-MM-DD)', date('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (false === $this->lock()) {
            $output->writeln('El comando ya está en ejecución. Abortando.');
            return Command::FAILURE;
        }

        $date = $input->getArgument('date');
        $reportes = $this->service->generarReporteDiario(new \DateTimeImmutable($date));
        $count = count($reportes);

        $output->writeln("[$date] Se generaron {$count} reportes diarios.");
        $this->release();

        return Command::SUCCESS;
    }
}
