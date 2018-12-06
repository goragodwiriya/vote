<?php
/**
 * @filesource Modules/Index/Models/People.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\People;

/**
 * Model สำหรับ Vote.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Somtum\Model
{
    /**
     * @var array
     */
    private $datas;

    /**
     * โหลดข้อมูล people.json.
     */
    public function __construct()
    {
        if (is_file(ROOT_PATH.'datas/people.json')) {
            // อ่านข้อมูล JSON
            $this->datas = json_decode(file_get_contents(ROOT_PATH.'datas/people.json'), true);
        } else {
            $this->datas = array(
                'config' => array(),
                'datas' => array(),
            );
        }
    }

    /**
     * คืนค่าข้อมูล config.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key)
    {
        return isset($this->datas['config'][$key]) ? $this->datas['config'][$key] : '';
    }

    /**
     * แก้ไข config.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setConfig($key, $value)
    {
        $this->datas['config'][$key] = $value;
    }

    /**
     * คืนค่ารายชื่อคนโหวต ทั้งหมด.
     *
     * @return array
     */
    public function getDatas()
    {
        return $this->datas['datas'];
    }

    /**
     * เพิ่ม/แก้ไข ข้อมูลคนโหวต.
     *
     * @param int   $id     0=ใหม่, >0 แก้ไข
     * @param array $values
     */
    public function setDatas($id, $values)
    {
        if (isset($this->datas['datas'][$id])) {
            unset($this->datas['datas'][$id]);
        }
        $this->datas['datas'][$values['number']] = array(
            'name' => $values['name'],
            'picture' => $values['picture'],
        );
        // เรียงลำดับข้อมูล
        ksort($this->datas['datas']);
    }

    /**
     * ตรวจสอบว่ามีรายการที่ต้องการหรือไม่
     * คืนค่า true ถ้ามี.
     *
     * @param  $id
     *
     * @return bool
     */
    public function exists($id)
    {
        return isset($this->datas['datas'][$id]);
    }

    /**
     * ลบรายการที่ $id.
     *
     * @param $id
     */
    public function delete($id)
    {
        unset($this->datas['datas'][$id]);
    }

    /**
     * คืนค่ารายการที่เลือก
     *
     * @param  $id
     *
     * @return array
     */
    public function get($id)
    {
        if ($id == 0) {
            $number = 1;
            while (isset($this->datas['datas'][$number])) {
                ++$number;
            }

            return array(
                'id' => 0,
                'number' => $number,
                'picture' => '',
                'name' => '',
            );
        } elseif (isset($this->datas['datas'][$id])) {
            $this->datas['datas'][$id]['id'] = $id;
            $this->datas['datas'][$id]['number'] = $id;

            return $this->datas['datas'][$id];
        }

        return false;
    }

    /**
     * คืนค่าชื่อ ที่ $id.
     *
     * @param  $id
     *
     * @return array
     */
    public function getName($id)
    {
        return isset($this->datas['datas'][$id]) ? $this->datas['datas'][$id]['name'] : '';
    }

    /**
     * คืนค่ารายชื่อทั้งหมด สำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelect()
    {
        $result = array();
        foreach ($this->datas['datas'] as $key => $values) {
            $result[$key] = $values['name'];
        }

        return $result;
    }

    /**
     * บันทีก people.json.
     *
     * @return int
     */
    public function save()
    {
        return file_put_contents(ROOT_PATH.'datas/people.json', json_encode($this->datas));
    }
}
