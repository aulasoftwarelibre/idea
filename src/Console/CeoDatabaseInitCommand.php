<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Console;

use App\Entity\Degree;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CeoDatabaseInitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ceo:database:init')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $manager->getRepository(Degree::class);

        $degrees = json_decode(static::$degrees)->degrees;

        foreach ($degrees as $degree) {
            $name = $degree->name;

            if ($repository->findOneBy(['name' => $name])) {
                continue;
            }

            $entity = new Degree();
            $entity->setName($name);

            $manager->persist($entity);

            $output->writeln("Creado '{$entity->getName()}");
        }

        $manager->flush();
    }

    public static $degrees = <<<EOD
{
 "degrees": [
  {
   "name": "Grado en Administración y Dirección de Empresas",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=79:memoria-de-ade&catid=10"
  },
  {
   "name": "Grado en Biología",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=72:memoria-de-biologia&catid=9"
  },
  {
   "name": "Grado en Bioquímica",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=73:memoria-de-bioquimica&catid=9"
  },
  {
   "name": "Grado en Ciencias Ambientales",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=74:memoria-de-ciencias-ambientales&catid=9"
  },
  {
   "name": "Grado en Cine y Cultura",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=105:grado-en-cine-y-cultura&catid=6"
  },
  {
   "name": "Grado en Derecho",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=78:memoria-de-derecho&catid=10"
  },
  {
   "name": "Grado en Educación Infantil",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=82:memoria-de-educacion-infantil&catid=10"
  },
  {
   "name": "Grado en Educación Infantil (Itinerario Bilingüe)",
   "url": "http://www.uco.es/educacion/geinfantil/Bilingue/BilingueGrInfantil.html"
  },
  {
   "name": "Grado en Educación Primaria",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=81:memoria-de-educacion-primaria&catid=10"
  },
  {
   "name": "Grado en Educación Primaria (Itinerario Bilingüe)",
   "url": "http://www.uco.es/educacion/geprimaria/Bilingue/BilingueGrPrimaria.html"
  },
  {
   "name": "Grado en Educación Social",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=80:memoria-de-educacion-social&catid=10"
  },
  {
   "name": "Grado en Enfermería",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=30:memoria-enfermeria&catid=7"
  },
  {
   "name": "Grado en Estudios Ingleses",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=96:memoria-de-estudios-ingleses&catid=6"
  },
  {
   "name": "Grado en Filología Hispánica",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=97:memoria-de-filologia-hispanica&catid=6"
  },
  {
   "name": "Grado en Fisica",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=75:memoria-de-fisica&catid=9"
  },
  {
   "name": "Grado en Fisioterapia",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=103:grado-en-fisioterapia&catid=7"
  },
  {
   "name": "Grado en Gestión Cultural",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=61:memriagestioncultural&catid=6"
  },
  {
   "name": "Grado en Historia",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=32:memoria-historia&catid=6"
  },
  {
   "name": "Grado en Historia del Arte",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=63:memoria-historia-del-arte&catid=6"
  },
  {
   "name": "Grado en Ingeniería Agroalimentaria y del Medio Rural",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=85:memoria-de-ingenieria-agroalimentaria-y-del-medio-rural&catid=8"
  },
  {
   "name": "Grado en Ingeniería Civil",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=91:memoria-de-ingenieria-civil&catid=8"
  },
  {
   "name": "Grado en Ingeniería Eléctrica",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=88:memoria-de-ingenieria-electrica&catid=8"
  },
  {
   "name": "Grado en Ingeniería Electrónica Industrial",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=89:memoria-de-ingenieria-electronica-industrial&catid=8"
  },
  {
   "name": "Grado en Ingeniería Forestal",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=86:memoria-de-ingenieria-forestal&catid=8"
  },
  {
   "name": "Grado en Ingeniería Informática",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=87:memoria-de-ingenieria-informatica&catid=8"
  },
  {
   "name": "Grado en Ingeniería Mecánica",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=90:memoria-de-ingenieria-mecanica&catid=8"
  },
  {
   "name": "Grado en Ingeniería en Recursos Energéticos y Mineros",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=92:memoria-de-ingenieria-en-recursos-energeticos-y-mineros&catid=8"
  },
  {
   "name": "Grado en Medicina",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=60:memoria-medicina&catid=7"
  },
  {
   "name": "Grado en Química",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=76:memoria-de-quimica&catid=9"
  },
  {
   "name": "Grado en Relaciones Laborales y Recursos Humanos",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=83:memoria-de-relaciones-laborales-y-rrhh&catid=10"
  },
  {
   "name": "Grado en Traducción e Interpretación",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=65:memoria-traduccion-e-interpretacion&catid=6"
  },
  {
   "name": "Grado en Turismo",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=84:memoria-de-turismo&catid=10"
  },
  {
   "name": "Grado en Veterinaria",
   "url": "http://www.uco.es/grados/index.php?option=com_content&view=article&id=29:memoria-veterinaria&catid=7"
  }
 ]
}
EOD;
}
