<?php
class PaymentAction extends AdminAction{
    public function offline(){
	
		 if(isset($_POST['bank'])){ 
            $bank_arr = array();
            foreach($_POST['bank'] as $k=>$v){
                $bank_arr[$k]=array(
                                'bank'=>stripslashes($v), 
                                'payee'=>stripslashes($_POST['payee'][$k]),
                                'account'=>stripslashes($_POST['account'][$k]),
                                'address'=>stripslashes($_POST['address'][$k]),
                                );
            }
            $info = $_POST['info'];   
            $this->saveConfig($bank_arr,$info);
            $this->success("操作成功",__URL__);
            exit;
        }
        
        import("ORG.Net.Keditor");
        $ke=new Keditor();
        $ke->id="info";
        $ke->width="700px";
        $ke->height="300px";
        $ke->items="['source', '|', 'fullscreen', 'undo', 'redo', 'print', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', '|', 'selectall', '-',
        'title', 'fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold',
        'italic', 'underline', 'strikethrough', 'removeformat', '|','table', 'hr', 'emoticons', 'link', 'unlink', '|', 'about']
        ";
        $ke->resizeMode=1;

        $ke->jspath="/res/kindeditor/kindeditor.js";
        $ke->form="bankForm";
        $keshow=$ke->show();
        $this->assign("keshow",$keshow);
            
            
        $config = FS("data/conf/banks");
        $this->assign('bank', $config['BANK']);
        $this->assign('info', $config['BANK_INFO']);
        $this->display();
    }
	
    private function saveConfig($arr,$info){
        $config['BANK'] = $arr;
        $config['BANK_INFO'] = $info; 
        FS("banks", $config, "data/conf/"); 
    }

    public function online(){
        $payconfig = FS("data/conf/payment");

        $this->assign('guofubao_config',$payconfig['guofubao']);
        $this->assign('ips_config',$payconfig['ips']);
        $this->assign('chinabank_config',$payconfig['chinabank']);
        $this->assign('baofoo_config', $payconfig['baofoo']);
        $this->assign('shengpay_config', $payconfig['shengpay']);
        $this->assign('tenpay_config', $payconfig['tenpay']);
        $this->assign('ecpss_config', $payconfig['ecpss']);
        $this->assign('easypay_config', $payconfig['easypay']);
        $this->assign('cmpay_config', $payconfig['cmpay']);
        $this->assign('allinpay_config',$payconfig['allinpay']);
        $this->display();
    }
    public function save(){
        FS("payment",$_POST['pay'],"data/conf/");
        alogs("Payonline",0,1,'执行了第三方支付接口参数的编辑操作！');//管理员操作日志
        $this->success("操作成功",__URL__."/index/");
    }
    
}
?>