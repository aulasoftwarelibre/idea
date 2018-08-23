<?php

/*
 * This file is part of the `idea` project.
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
        $masters = json_decode(static::$masters)->masters;

        $studies = array_merge($degrees, $masters);

        foreach ($studies as $study) {
            $name = $study->name;

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

    public static $masters = <<<EOD
{
 "masters": [
  {
   "name": "Doble Máster en Ingeniería Agronómica e Hidráulica Ambiental (Especialidad Gestión Integral de Cuencas)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Doble Máster en Ingeniería Agronómica e Ingeniería y Gestión de la Cadena Agroalimentaria",
   "url": "https://www.uco.es/estudios/idep/masteres/doble-ingenieria-agronomica-e-ingenieria-y-gestion-cadena-agroalimentaria"
  },
  {
   "name": "Doble Máster en Ingeniería Agronómica y Estrategias para el Desarrollo Rural y Territorial",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Doble Máster en Ingeniería Agronómica y Producción, Protección y Mejora Vegetal",
   "url": "https://www.uco.es/estudios/idep/masteres/node/213"
  },
  {
   "name": "Doble Máster en Ingeniería Agronómica y Representación y Diseño en Ingeniería y Arquitectura",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Doble Máster en Ingeniería de Montes e Hidráulica Ambiental (Especialidad Gestión Integral de Cuencas)",
   "url": "https://www.uco.es/estudios/idep/masteres/doble-ingenieria-montes-e-hidraulica-ambiental"
  },
  {
   "name": "Doble Máster en Ingeniería de Montes e Incendios Forestales, Ciencia y Gestión Integral (Interuniversitario - UCO/ULE/UDL)",
   "url": "http://goo.gl/wcVGjE"
  },
  {
   "name": "Doble Máster en Ingeniería de Montes y Estrategias para el Desarrollo Rural y Territorial",
   "url": "https://www.uco.es/estudios/idep/masteres/doble-ingenieria-montes-y-estrategias-desarrollo-rural-y-territorial"
  },
  {
   "name": "Doble Máster en Ingeniería de Montes y Geomática, Teledetección y Modelos Espaciales Aplicado a la Gestión Forestal",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Doble Máster en Ingeniería de Montes y Representación y Diseño en Ingeniería y Arquitectura",
   "url": "https://www.uco.es/estudios/idep/masteres/doble-ingenieria-montes-y-representacion-y-diseno-ingenieria-arquitectura"
  },
  {
   "name": "Doble Máster en Profesorado de Enseñanza Secundaria Obligatoria y Bachillerato, Formación Profesional y Enseñanza de Idiomas y Estudios Ingleses Avanzados",
   "url": "http://goo.gl/KM1305"
  },
  {
   "name": "Doble Máster en Profesorado de Enseñanza Secundaria Obligatoria y Bachillerato, Formación Profesional y Enseñanza de Idiomas y Química",
   "url": "http://goo.gl/EzQAe2"
  },
  {
   "name": "Doble Máster en Profesorado de Enseñanza Secundaria Obligatoria y Bachillerato, Formación Profesional y Enseñanza de Idiomas y Representación y Diseño en Ingeniería y Arquitectura",
   "url": "https://goo.gl/hEXdkI"
  },
  {
   "name": "Máster en Abogacía",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Administración y Dirección de Empresas (MBA)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Agroalimentación (Interuniversitario - UCO/UCA)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Agroecología: un Enfoque para la Sustentabilidad Rural (Interuniversitario - UCO/UNIA/UPO)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Asesoría Jurídica de Empresas",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Biotecnología",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Cambio Global: Recursos Naturales y Sostenibilidad",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Cinematografía",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Comercio Exterior e Internacionalización de Empresas",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Cultura de Paz. Conflictos, Educación y Derechos Humanos (Interuniversitario - UCO/UGR/UCA/UMA)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Educación Ambiental (Interuniversitario - UCA/UCO/UGR/UAL/UPO/UHU/UMA)",
   "url": "https://www.uco.es/estudios/idep/masteres/educacion-ambiental"
  },
  {
   "name": "Máster en Educación Inclusiva",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Electroquímica. Ciencia y Tecnología (Interuniversitario - UCO/UAB/UAM/UA/UB/UBU/UMU/UVEG/UPCT)",
   "url": "https://www.uco.es/estudios/idep/masteres/electroquimica-ciencia-tecnologia"
  },
  {
   "name": "Máster en Energías Renovables Distribuidas",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Español: Lengua, Literatura, Historia o Enseñanza",
   "url": "https://www.uco.es/estudios/idep/masteres/espanol-lengua-literatura-historia-o-ensenanza"
  },
  {
   "name": "Máster en Especialización en Educación: Juego, Juguetes, Segundas Lenguas e Intercultura (PETaL) (Interuniversitario Internacional UCO-IPL-MU)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Estrategias para el Desarrollo Rural y Territorial",
   "url": "https://www.uco.es/estudios/idep/masteres/estrategias-desarrollo-rural-y-territorial"
  },
  {
   "name": "Máster en Estudios Ingleses Avanzados (Lingüística Cognitiva/Literatura) y Educación Bilingüe",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Geomática, Teledetección y Modelos Espaciales Aplicados a la Gestión Forestal",
   "url": "https://www.uco.es/estudios/idep/masteres/geomatica"
  },
  {
   "name": "Máster en Gestión del Patrimonio Desde el Municipio",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Hidráulica Ambiental (Interuniversitario - UCO/UGR/UMA)",
   "url": "https://www.uco.es/estudios/idep/masteres/hidraulica-ambiental"
  },
  {
   "name": "Máster en Incendios Forestales, Ciencia y Gestión Integral (Interuniversitario - UCO/ULE/UDL)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Ingeniería Agronómica",
   "url": "https://www.uco.es/estudios/idep/masteres/node/193"
  },
  {
   "name": "Máster en Ingeniería de Minas",
   "url": "http://www.uco.es/politecnica-belmez/magua/index.html"
  },
  {
   "name": "Máster en Ingeniería de Montes",
   "url": "https://www.uco.es/estudios/idep/masteres/node/194"
  },
  {
   "name": "Máster en Ingeniería Industrial",
   "url": "http://www.uco.es/eps/node/1505"
  },
  {
   "name": "Máster en Ingeniería Informática",
   "url": "https://www.uco.es/estudios/idep/masteres/ingenieria-informatica"
  },
  {
   "name": "Máster en Ingeniería y Gestión de la Cadena Agroalimentaria",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Investigación Biomédica Traslacional",
   "url": "https://www.uco.es/estudios/idep/masteres/investigacion-biomedica-traslacional"
  },
  {
   "name": "Máster en Medicina, Sanidad y Mejora Animal",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Nutrición Humana",
   "url": "https://www.uco.es/estudios/idep/masteres/nutricion-humana"
  },
  {
   "name": "Máster en Olivicultura y Elaiotecnia",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Plasma, Láser y Tecnologías de Superficie (Interuniversitario - UCO/UPM)",
   "url": "https://www.uco.es/estudios/idep/masteres/plasma-laser-y-tecnologias-superficie"
  },
  {
   "name": "Máster en Prevención de Riesgos Laborales",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Producción, Protección y Mejora Vegetal",
   "url": "https://www.uco.es/estudios/idep/masteres/node/211"
  },
  {
   "name": "Máster en Profesorado de Enseñanza Secundaria Obligatoria y Bachillerato, Formación Profesional y Enseñanza de Idiomas",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Psicología Aplicada a la Educación y el Bienestar Social",
   "url": "https://www.uco.es/estudios/idep/masteres/psicologia-aplicada-educacion-y-bienestar-social"
  },
  {
   "name": "Máster en Psicología General Sanitaria",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Química (Interuniversitario - UCO/UAL/UCA/UHU/UJA/UMA)",
   "url": "https://www.uco.es/estudios/idep/masteres/quimica"
  },
  {
   "name": "Máster en Representación y Diseño en Ingeniería y Arquitectura (Interuniversitario - UCO/UAL/UMA)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster en Tecnología del Agua en Ingeniería Civil",
   "url": "https://www.uco.es/estudios/idep/masteres/tecnologia-del-agua"
  },
  {
   "name": "Máster en Traducción Especializada (Inglés / Francés / Alemán - Español)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster Universitario en Biotecnología por la Universidad de Córdoba y Laurea Magistrale en Biotecnologie per l'Ambiente e la Salute por la Universidad de Ferrara (Italia)",
   "url": "https://www.uco.es/estudios/idep/masteres/node/205"
  },
  {
   "name": "Máster Universitario en Comercio Exterior e Internacionalización de Empresas por la Universidad de Córdoba y Máster Administration et Echanges Internationaux por la Universidad Paris-Est Créteil Val de Marne (Francia)",
   "url": "https://www.uco.es/estudios/idep/masteres/listado-masteres/all"
  },
  {
   "name": "Máster Universitario en Comercio Exterior e Internacionalización de Empresas por la Universidad de Córdoba y Máster Europäisches Management de la Universidad Técnica de Ciencias Aplicadas Wildau (Alemania)",
   "url": "https://www.uco.es/estudios/idep/masteres/node/206"
  },
  {
   "name": "Máster Universitario en Gestión del Patrimonio desde el Municipio por la Universidad de Córdoba y Máster en Turismo Responsable y Desarrollo Humano por la Universidad de Abdelmaleck Essaädi de Tetuán (Marruecos)",
   "url": "https://www.uco.es/estudios/idep/masteres/node/207"
  }
 ]
}
EOD;
}
