<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace JamesHalsall\Licenser\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Licenser console command.
 *
 * Hooks the Licenser into the symfony console.
 *
 * @package JamesHalsall\Licenser\Command
 * @author  James Halsall <james.t.halsall@googlemail.com>
 * @see     JamesHalsall\Licenser\Licenser
 */
class LicenserCommand extends AbstractLicenserCommand
{
    /**
     * Configures the command interface
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('run')
            ->setDescription('Runs the licenser against the given source path')
            ->addOption(
                'replace-existing',
                'r',
                InputOption::VALUE_NONE,
                'Replace existing license headers'
            )
            ->addOption(
                'dry-run',
                '',
                InputOption::VALUE_NONE,
                'If specified, the command will report a list of affected files but will make no modifications'
            )
        ;
    }

    /**
     * Executes the command
     *
     * @param InputInterface  $input  An input stream
     * @param OutputInterface $output An output stream
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initializeLicenser($input, $output);

        $sources = $input->getArgument('sources');

        $this->licenser->process(
            $sources,
            (bool) $input->getOption('replace-existing'),
            (bool) $input->getOption('dry-run')
        );

        return 0;
    }
}
