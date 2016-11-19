<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**:根据 中华人民共和国国家标准《公民身份号码》（GB11643-1999） 进行初步验证
 *!建议之后进行进一步连接至公安部身份证数据库进行验证
 *!该方法只支持 18 位身份证
 * @string $id_card 身份证号
 * @return array ['state'=>boolean,'msg'=>string] 数组：状态(false:假,true:真)+提示
 */
function checkIdCard($id_card)
{
    $msg_false='身份证号错误！';
    $msg_warning='不支持15位身份证，请在派出所更换身份证。';
    $msg_true='身份证号正确！';
    //先判断长度
    $length=strlen($id_card);
    if( $length == 15
        || $length == 18
    );
    else
    {
        return array(
            'state'=>false,
            'msg'=>$msg_false
        );
    }
    //如果是15位，先判断是否符合15位正则
    if($length == 15)
    {
        $pattern_15="/^[1-9]\d{5}\d{2}((0[1-9])|1[012])(0[1-9]|[12][0-9]|3[01])\d{3}$/";
        if(preg_match($pattern_15,$id_card))
            return array(
                'state'=>false,
                'msg'=>$msg_warning
            );
        else
            return array(
                'state'=>false,
                'msg'=>$msg_false
            );
    }
    if($length == 18)
    {
        //先判断是否符合18位正则
        $pattern_18="/^[1-9]\d{5}[1-9]\d{3}((0[1-9])|1[012])(0[1-9]|[12][0-9]|3[01])\d{3}(\d|x|X)$/";
        if(!preg_match($pattern_18,$id_card))
            return array(
                'state'=>false,
                'msg'=>$msg_false
            );
    }
    //大区域编码验证
    $area_code_arr=array(11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65,71,81,82,91);
    $area_code=substr($id_card,0,2);
    if(!in_array($area_code,$area_code_arr))
        return array(
            'state'=>false,
            'msg'=>$msg_false
        );
    //验证日期是否存在，包括不能是当天往后，在闰年2月不能大于29日，非闰年2月不能大于28日
    //获取身份证号年月日期
    $the_date=substr($id_card,6,8);
    $year=substr($the_date,0,4);
    $month=ltrim(substr($the_date,4,2),'0');
    $day=ltrim(substr($the_date,6,2),'0');
    //检验
    if(!checkdate($month,$day,$year))
        return array(
            'state'=>false,
            'msg'=>$msg_false
        );
    //验证18位身份证：算出 尾数，进行验证。
    $fator_arr=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    $temp=array();
    for($j=0;$j<17;$j++)
    {
        $temp[]=$id_card[$j]*$fator_arr[$j];
    }
    $temp=array_sum($temp)%11;
    $verify_arr=array('1','0','X','9','8','7','6','5','4','3','2');
    if($verify_arr[$temp] == $id_card[17])
        return array(
            'state'=>true,
            'msg'=>$msg_true
        );
    else
        return array(
            'state'=>false,
            'msg'=>$msg_false
        );
}


/* 冒泡算法：结果从小到大，规则类似波浪推动的沙滩，初始第一次波浪之后，先初始阈值为 0，如果发现有左值比右边的大，就改变阈值并且完成波浪推动，如此往复，直到没有阈值改变的情况出现，说明沙滩平滑，阈值无需再改，无需推动波浪了。
 * @para $arr 传人进去排序的数组
 * @return $newArr 排序之后的数组
 */
function maopao($arr)
{
    //一共是多少趟
    for($i = count($arr)-1; $i>0; $i--){
        $flag = 0;
        //每一趟进行相邻两个数进行比较
        for($j = 0; $j < $i; $j++){
            if($arr[$j]>$arr[$j+1]){
                $temp = $arr[$j];
                $arr[$j] = $arr[$j+1];
                $arr[$j+1] =$temp;
                $flag = 1;
            }
        }
        if($flag == 0){
            break;
        }
    }
    return $arr;
}

/**快速排序：通过递归来实现，递归发生的子规则是：以第一个元素为基，比基数大的过滤到左边的数组，比基数小的过滤到右边的数组----一直到子底层，然后开始回层合并（左数组，基数，右数组）
 * @param $arr
 * @return array
 */
function quickSort($arr)
{
    //先判断是否需要继续进行
    $length = count($arr);
    if($length <= 1) {
        return $arr;
    }

    //选择第一个元素作为基准
    $base_num = $arr[0];
    //遍历除了标尺外的所有元素，按照大小关系放入两个数组内
    //初始化两个数组
    $left_array = array();  //小于基准的
    $right_array = array();  //大于基准的
    for($i=1; $i<$length; $i++) {
        if($base_num > $arr[$i]) {
            //放入左边数组
            $left_array[] = $arr[$i];
        } else {
            //放入右边
            $right_array[] = $arr[$i];
        }
    }
    //再分别对左边和右边的数组进行相同的排序处理方式递归调用这个函数
    $left_array = quick_sort($left_array);
    $right_array = quick_sort($right_array);
    //合并
    return array_merge($left_array, array($base_num), $right_array);
}

/**插入排序（蠕虫排序）：初始以第一个的元素为【既定结果数组】中的一个元素，然后取【既定结果数组】之后的值在【既定结果数组】中从末尾向头部开始比较，如果该值没有尾部的值大，就模拟蠕动，使该值向【既定结果数组】头部前进再次与左邻的元素值比较，如此往复，直到碰到比该值小的存在才停止蠕动；；循环调用该规则，直到所有元素蠕动一遍完成排序。
 * @param $arr
 * @return mixed
 */
function insertSort($arr)
{
    $len=count($arr);
    for($i=1; $i<$len; $i++) {
        $tmp = $arr[$i];
        //内层循环控制，比较并插入
        for($j=$i-1;$j>=0;$j--) {
            if($tmp < $arr[$j]) {
                //发现插入的元素要小，交换位置，将后边的元素与前面的元素互换
                $arr[$j+1] = $arr[$j];
                $arr[$j] = $tmp;
            } else {
                //如果碰到不需要移动的元素，由于是已经排序好是数组，则前面的就不需要再次比较了。
                break;
            }
        }
    }
    return $arr;
}


/**选择排序：有着枚举思想，虚拟该数组为两部分，初始第一部分数组只有一个元素（随外循环，不断增大），不停枚举第二部分数组元素（随外循环，不断减小）中的最小值与第一部分数组最后一个元素进行换位，换位结束则排序完成。
 * @param $arr
 * @return mixed
 */
function selectSort($arr)
{
//双重循环完成，外层控制轮数，内层控制比较次数
    $len=count($arr);
    for($i=0; $i<$len-1; $i++) {
        //先假设最小的值的位置
        $p = $i;

        for($j=$i+1; $j<$len; $j++) {
            //$arr[$p] 是当前已知的最小值
            if($arr[$p] > $arr[$j]) {
                //比较，发现更小的,记录下最小值的位置；并且在下次比较时采用已知的最小值进行比较。
                $p = $j;
            }
        }
        //已经确定了当前的最小值的位置，保存到$p中。如果发现最小值的位置与当前假设的位置$i不同，则位置互换即可。
        if($p != $i) {
            $tmp = $arr[$p];
            $arr[$p] = $arr[$i];
            $arr[$i] = $tmp;
        }
    }
    //返回最终结果
    return $arr;
}