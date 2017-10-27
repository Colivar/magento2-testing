<?php
//namespace Magento\Webapi\Routing;
//
//class CoreRoutingTest extends\Magento\TestFramework\TestCase\WebapiAbstract
//{
//    public function testBasicRoutingExplicitPath()
//    {
//        $itemId = 1;
//        $serviceInfo = [
//            ‘rest’ => [
//                ‘resourcePath’ => ‘/V1/testmodule1/’ .$itemId,
//                ‘httpMethod’ =>\Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
//            ],
//            ‘soap’ => [
//                ‘service’ => ‘testModule1AllSoapAndRestV1′,
//                ‘operation’ =>‘testModule1AllSoapAndRestV1Item’,
//            ],
//        ];
//        $requestData = [‘itemId’ => $itemId];
//        $item = $this->_webApiCall($serviceInfo,$requestData);
//        $this->assertEquals(‘testProduct1′, $item[‘name’],'retrieved unsuccessfully');
//   }
//}