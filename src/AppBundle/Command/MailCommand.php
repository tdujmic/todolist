<?php
// Change the namespace according to your bundle
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Add the required classes
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
// Add the Container
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class MailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:send-mail')
            // the short description shown while running "php bin/console list"
            ->setHelp("This command allows you to send emails day before TODO task comes")
            // the full command description shown when running the command with
            ->setDescription('Send email to admin with list f TODOs. Command can be used from cron job.')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([
            '',
            'Email send command',
            '==================',
            '',
        ]);

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        //get todos which have dueDate tomorow
        $todos = $em
                ->createQuery('SELECT t.id, DATE_DIFF( t.dueDate,CURRENT_DATE()) as days FROM AppBundle:Todolist t WHERE DATE_DIFF( t.dueDate,CURRENT_DATE()) = 1 ')
                ->getResult();

        $output->writeln("Count of TODOs to send email : ".count($todos));

        foreach ($todos as $item) {

            //Get TODO with id from list of ids f TODOs with dueDate tommorow
            $todoRepo = $em->getRepository("AppBundle:Todolist");
            $todo = $todoRepo->find($item['id']);
            //Set emailsent flag to Yes to avoid multiple email send during a cron job for same TODO
            $todo->setMailSent(1);
            $em->flush();

            $output->writeln("Todo : ".$todo->getTitle());

            $subject = 'You have TODO: '.$todo->getTitle();

            $messageBody = 'Todo: '.$todo->getId().' '.$todo->getTitle().'<br>'.$todo->getDescription().'<br>';

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->getEmailSender())
                ->setTo($todo->getEmail())
                ->setBody($messageBody, 'text/html');

            $this->getContainer()->get('mailer')->send($message);
            $output->writeln("Mail for todo ID: ".$todo->getId().' sent to: '.$todo->getEmail().chr(10).chr(13));

        }
    }


    /**
     * Get email sender from parameters.yml
     *
     * @return string
     */
    public function getEmailSender()
    {
        return $this->getContainer()->getParameter('mailer_sender_address');

    }



}
