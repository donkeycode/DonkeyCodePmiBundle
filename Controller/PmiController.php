<?php

namespace DonkeyCode\PmiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\Response;
use PDO;
use DonkeyCode\PmiBundle\Form\QueryType;

class PmiController extends Controller
{
    /**
     * @Route("/pmi", name="pmi_home")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Template()
     */
    public function tablesAction()
    {
        return [
            'tables' => $this->propelExectute('SHOW TABLES'),
        ];
    }

    /**
     * @Route("/pmi/query", name="pmi_query")
     * @Template("DonkeyCodePmiBundle:Pmi:results.html.twig")
     */
    public function queryAction(Request $request)
    {
        $form = $this->createForm(QueryType::class, null, [
            'action' => $this->generateUrl('pmi_query'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData()['query'];
            $datas = $this->propelExectute($query, PDO::FETCH_ASSOC);

            if (count($datas) > 0) {
                $cols = array_keys($datas[0]);
            }

            return [
                'table'  => 'Query',
                'query'  => $query,
                'datas'  => $datas,
                'cols'   => $cols ?? [],
                'form'   => $form->createView(),
            ];
        }

        return [
            'table'  => 'Query',
            'query'  => $query,
            'datas'  => [],
            'cols'   => [],
            'form'   => $form->createView(),
        ];
    }

    /**
     * @Route("/pmi/{table}", name="pmi_table")
     * @Template()
     */
    public function tableAction($table)
    {
        return [
            'table'  => $table,
            'schema' => $this->propelExectute('DESCRIBE '.$table),
        ];
    }

    /**
     * @Route("/pmi/{table}/select", name="pmi_table_select")
     * @Template("DonkeyCodePmiBundle:Pmi:results.html.twig")
     */
    public function tableSelectAction(Request $request, $table)
    {
        $query = 'SELECT * FROM '.$table.' WHERE 1 LIMIT 0, 30';
        $datas = $this->propelExectute($query, PDO::FETCH_ASSOC);

        $form = $this->createForm(QueryType::class, [
            'query' => $query
        ], [
            'action' => $this->generateUrl('pmi_query'),
        ]);
        $form->handleRequest($request);

        if (count($datas) > 0) {
            $cols = array_keys($datas[0]);
        }

        return [
            'table'  => $table,
            'query'  => $query,
            'datas'  => $datas,
            'cols'   => $cols ?? [],
            'form'   => $form->createView(),
        ];
    }

    private function propelExectute($sql, $fetchMode = PDO::FETCH_BOTH)
    {
        $con = Propel::getConnection();
        $stmt = $con->prepare($sql);

        $res = $stmt->execute();

        return $stmt->fetchAll($fetchMode);
    }
}